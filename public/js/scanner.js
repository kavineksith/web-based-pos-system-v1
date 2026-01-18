/**
 * Barcode/QR Code Scanner Handler
 * Handles automatic detection and input from barcode/QR scanners
 * Supports subdirectory deployment and proper error handling
 */

class ScannerHandler {
    constructor(options = {}) {
        this.options = {
            inputSelector: options.inputSelector || '#scanner-input',
            onScan: options.onScan || null,
            minLength: options.minLength || 3,
            maxLength: options.maxLength || 100,
            delay: options.delay || 100, // milliseconds to wait after Enter key
            autoSubmit: options.autoSubmit !== false,
            ...options
        };
        
        this.scanBuffer = '';
        this.lastKeyTime = 0;
        this.isScanning = false;
        this.inputElement = null;
        this.init();
    }
    
    init() {
        // Find or create input element
        this.inputElement = document.querySelector(this.options.inputSelector);
        
        if (!this.inputElement) {
            // Create hidden input if it doesn't exist
            this.inputElement = document.createElement('input');
            this.inputElement.type = 'text';
            this.inputElement.id = this.options.inputSelector.replace('#', '');
            this.inputElement.style.position = 'absolute';
            this.inputElement.style.left = '-9999px';
            this.inputElement.style.opacity = '0';
            this.inputElement.setAttribute('autocomplete', 'off');
            document.body.appendChild(this.inputElement);
        }
        
        // Focus on input for scanner
        this.inputElement.focus();
        
        // Store bound methods for proper event listener removal
        this.boundHandleKeyDown = (e) => this.handleKeyDown(e);
        this.boundHandleKeyPress = (e) => this.handleKeyPress(e);
        this.boundHandleInput = (e) => this.handleInput(e);
        
        // Listen for keyboard events
        this.inputElement.addEventListener('keydown', this.boundHandleKeyDown);
        this.inputElement.addEventListener('keypress', this.boundHandleKeyPress);
        this.inputElement.addEventListener('input', this.boundHandleInput);
        
        // Keep input focused
        document.addEventListener('click', () => {
            if (!this.isScanning) {
                this.inputElement.focus();
            }
        });
    }
    
    handleKeyDown(e) {
        // Reset buffer on Escape
        if (e.key === 'Escape') {
            this.reset();
            return;
        }
        
        // Handle Enter key (scanner typically sends Enter at end)
        if (e.key === 'Enter') {
            e.preventDefault();
            this.processScan();
            return;
        }
    }
    
    handleKeyPress(e) {
        const currentTime = Date.now();
        
        // Detect if this is a fast scan (scanner) or manual typing
        if (currentTime - this.lastKeyTime > this.options.delay) {
            // Likely manual typing, reset buffer
            this.scanBuffer = '';
        }
        
        this.lastKeyTime = currentTime;
        this.isScanning = true;
    }
    
    handleInput(e) {
        const value = e.target.value;
        
        // If value is cleared, reset buffer
        if (!value) {
            this.scanBuffer = '';
            return;
        }
        
        this.scanBuffer = value;
    }
    
    processScan() {
        const code = this.scanBuffer.trim();
        
        // Validate length
        if (code.length < this.options.minLength || code.length > this.options.maxLength) {
            this.reset();
            return;
        }
        
        // Call callback if provided
        if (this.options.onScan && typeof this.options.onScan === 'function') {
            this.options.onScan(code);
        }
        
        // Dispatch custom event
        const event = new CustomEvent('scanner:scan', {
            detail: { code: code }
        });
        document.dispatchEvent(event);
        
        // Reset after processing
        setTimeout(() => {
            this.reset();
        }, 100);
    }
    
    reset() {
        this.scanBuffer = '';
        this.isScanning = false;
        if (this.inputElement) {
            this.inputElement.value = '';
            this.inputElement.focus();
        }
    }
    
    focus() {
        if (this.inputElement) {
            this.inputElement.focus();
        }
    }
    
    destroy() {
        if (this.inputElement && this.inputElement.parentNode) {
            // Use stored bound methods to properly remove event listeners
            this.inputElement.removeEventListener('keydown', this.boundHandleKeyDown || this.handleKeyDown.bind(this));
            this.inputElement.removeEventListener('keypress', this.boundHandleKeyPress || this.handleKeyPress.bind(this));
            this.inputElement.removeEventListener('input', this.boundHandleInput || this.handleInput.bind(this));
        }
    }
}

// Auto-detect and register scanner device
class ScannerDeviceManager {
    constructor() {
        this.devices = [];
        this.defaultDevice = null;
        this.token = localStorage.getItem('auth_token');
        this.basePath = (window.BASE_PATH && window.BASE_PATH !== '/') ? window.BASE_PATH : (window.location.pathname.match(/^\/[^\/]+/) || [''])[0] || '';
        this.isConnected = false; // Track scanner connection status
        this.init();
    }
    
    async init() {
        try {
            await this.loadDevices();
            await this.detectAndRegister();
        } catch (error) {
            console.error('Error initializing scanner device manager:', error);
            this.isConnected = false;
        }
    }
    
    async loadDevices() {
        try {
            const response = await fetch((this.basePath || '') + '/api/scanners?type=scanner', {
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + this.token,
                    'Content-Type': 'application/json'
                }
            });
            
            if (!response.ok) {
                // If API call fails, we can still use manual input, so set as connected
                console.warn('Failed to load scanner devices:', response.status);
                this.devices = [];
                this.defaultDevice = null;
                this.isConnected = false; // Scanner not available but manual input works
                return;
            }
            
            const data = await response.json();
            if (data.success) {
                this.devices = data.data || [];
                this.defaultDevice = this.devices.find(d => d.is_default) || this.devices[0] || null;
                this.isConnected = this.devices.length > 0; // Set connection status
            } else {
                this.devices = [];
                this.defaultDevice = null;
                this.isConnected = false;
                console.warn('No scanner devices found:', data.message || 'No devices registered');
            }
        } catch (error) {
            console.error('Failed to load scanner devices:', error);
            this.devices = [];
            this.defaultDevice = null;
            this.isConnected = false; // Set connection status
        }
    }
    
    async detectAndRegister() {
        // Generate device identifier from browser/OS info
        const deviceId = this.generateDeviceId();
        const deviceName = this.getDeviceName();
        
        try {
            const response = await fetch((this.basePath || '') + '/api/scanners/register', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + this.token,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    device_identifier: deviceId,
                    device_name: deviceName,
                    device_type: 'scanner',
                    driver: 'browser_hid',
                    settings: {
                        user_agent: navigator.userAgent,
                        platform: navigator.platform,
                        language: navigator.language
                    }
                })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            if (result.success) {
                this.isConnected = true; // Set connection status
            } else {
                console.warn('Scanner registration failed:', result.message);
                this.isConnected = false;
            }
        } catch (error) {
            console.error('Failed to register device:', error);
            this.isConnected = false; // Set connection status
        }
    }
    
    generateDeviceId() {
        // Use combination of browser and screen info
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        ctx.textBaseline = 'top';
        ctx.font = '14px Arial';
        ctx.fillText('Device ID', 2, 2);
        
        const fingerprint = [
            navigator.userAgent,
            navigator.platform,
            screen.width + 'x' + screen.height,
            new Date().getTimezoneOffset(),
            navigator.language,
            canvas.toDataURL()
        ].join('|');
        
        // Simple hash
        let hash = 0;
        for (let i = 0; i < fingerprint.length; i++) {
            const char = fingerprint.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }
        
        return 'browser_' + Math.abs(hash).toString(36);
    }
    
    getDeviceName() {
        const os = this.getOS();
        const browser = this.getBrowser();
        return `${browser} on ${os}`;
    }
    
    getOS() {
        const userAgent = navigator.userAgent;
        if (userAgent.indexOf('Win') !== -1) return 'Windows';
        if (userAgent.indexOf('Mac') !== -1) return 'macOS';
        if (userAgent.indexOf('Linux') !== -1) return 'Linux';
        if (userAgent.indexOf('Android') !== -1) return 'Android';
        if (userAgent.indexOf('iOS') !== -1) return 'iOS';
        return 'Unknown OS';
    }
    
    getBrowser() {
        const userAgent = navigator.userAgent;
        if (userAgent.indexOf('Chrome') !== -1) return 'Chrome';
        if (userAgent.indexOf('Firefox') !== -1) return 'Firefox';
        if (userAgent.indexOf('Safari') !== -1) return 'Safari';
        if (userAgent.indexOf('Edge') !== -1) return 'Edge';
        return 'Unknown Browser';
    }
    
    async setDefault(deviceId) {
        try {
            const response = await fetch((this.basePath || '') + '/api/scanners/set-default', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + this.token,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    device_id: deviceId,
                    device_type: 'scanner'
                })
            });
            const data = await response.json();
            if (data.success) {
                await this.loadDevices();
                return true;
            }
            return false;
        } catch (error) {
            console.error('Failed to set default device:', error);
            return false;
        }
    }
}

// Global scanner instance
let scannerHandler = null;
let scannerDeviceManager = null;

// Initialize scanner on page load
document.addEventListener('DOMContentLoaded', function() {
    scannerDeviceManager = new ScannerDeviceManager();
});

// Utility function to get scanner status
function getScannerStatus() {
    if (scannerDeviceManager && scannerDeviceManager.isConnected !== undefined) {
        return scannerDeviceManager.isConnected ? 'Connected' : 'Not Connected';
    }
    return 'Not Connected';
}

// Utility function to check if scanner is available
function isScannerAvailable() {
    if (!scannerDeviceManager) {
        return false;
    }
    return scannerDeviceManager.isConnected === true;
}

