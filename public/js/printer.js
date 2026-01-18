/**
 * Printer Handler
 * Handles bill printing functionality
 * Supports subdirectory deployment, PDF fallback, and proper error handling
 */

class PrinterHandler {
    constructor() {
        // Validate and sanitize token
        const token = localStorage.getItem('auth_token');
        this.token = token && typeof token === 'string' ? token : null;
        
        if (!this.token) {
            console.warn('No authentication token found');
        }
        this.defaultPrinter = null;
        this.basePath = (window.BASE_PATH && window.BASE_PATH !== '/') ? window.BASE_PATH : (window.location.pathname.match(/^\/[^\/]+/) || [''])[0] || '';
        this.isConnected = false; // Track printer connection status
        this.printQueue = []; // Queue for pending print jobs
        this.isPrinting = false; // Flag to prevent concurrent prints
        this.init();
    }
    
    async init() {
        await this.loadDefaultPrinter();
    }
    
    async loadDefaultPrinter() {
        try {
            const response = await fetch((this.basePath || '') + '/api/printers/default?type=printer', {
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + this.token,
                    'Content-Type': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            if (data.success && data.data) {
                this.defaultPrinter = data.data;
                this.isConnected = true; // Set connection status
            } else {
                this.defaultPrinter = null;
                this.isConnected = false; // No printer available
                console.warn('No default printer found:', data.message || 'No printer configured');
            }
        } catch (error) {
            console.error('Failed to load default printer:', error);
            this.defaultPrinter = null;
            this.isConnected = false; // Set connection status
        }
    }
    
    async printBill(billId, printData = null) {
        // Add to print queue to handle concurrent requests
        return new Promise((resolve, reject) => {
            this.printQueue.push({
                billId,
                printData,
                resolve,
                reject
            });
            
            // Process queue if not already processing
            if (!this.isPrinting) {
                this.processPrintQueue();
            }
        });
    }
    
    async processPrintQueue() {
        if (this.printQueue.length === 0) {
            this.isPrinting = false;
            return;
        }
        
        this.isPrinting = true;
        const { billId, printData, resolve, reject } = this.printQueue.shift();
        
        try {
            // Check if printer is connected, otherwise use PDF fallback
            if (!this.isConnected) {
                console.warn('Printer not connected, using PDF fallback');
                const result = await this.printBillAsPDF(billId, printData);
                resolve(result);
            } else {
                // Load bill data if not provided
                let actualPrintData = printData;
                if (!actualPrintData) {
                    const response = await fetch((this.basePath || '') + '/api/printers/bill/' + billId, {
                        method: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + this.token,
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    if (!response.ok) {
                        console.warn('Failed to load bill data, falling back to PDF:', response.status);
                        const result = await this.printBillAsPDF(billId, printData);
                        resolve(result);
                        return;
                    }
                    
                    const data = await response.json();
                    if (!data.success) {
                        console.warn('Failed to load bill data, falling back to PDF:', data.message);
                        const result = await this.printBillAsPDF(billId, printData);
                        resolve(result);
                        return;
                    }
                    actualPrintData = data.data;
                }
                
                // Generate print-ready HTML
                const printHtml = this.generatePrintHTML(actualPrintData);
                
                // Open print window
                const printWindow = window.open('', '_blank');
                printWindow.document.write(printHtml);
                printWindow.document.close();
                
                // Wait for content to load, then print
                printWindow.onload = function() {
                    setTimeout(() => {
                        printWindow.print();
                        // Optionally close after printing
                        // printWindow.close();
                    }, 250);
                };
                
                // Mark bill as printed
                await this.markBillAsPrinted(billId);
                
                resolve(true);
            }
        } catch (error) {
            console.error('Print error, falling back to PDF:', error);
            try {
                // Try PDF fallback if direct printing fails
                const result = await this.printBillAsPDF(billId, printData);
                resolve(result);
            } catch (pdfError) {
                console.error('PDF fallback also failed:', pdfError);
                reject(pdfError);
            }
        }
        
        // Process next item in queue
        setTimeout(() => {
            this.processPrintQueue();
        }, 100); // Small delay between prints
    }
    
    async printBillAsPDF(billId, printData = null) {
        try {
            // If no print data provided, fetch it
            if (!printData) {
                const response = await fetch((this.basePath || '') + '/api/printers/bill/' + billId, {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + this.token,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.message || 'Failed to load bill data');
                }
                printData = data.data;
            }
            
            // Generate print-ready HTML
            const printHtml = this.generatePrintHTML(printData);
            
            // Open PDF viewer in new window
            const printWindow = window.open('', '_blank');
            if (!printWindow) {
                // If popup is blocked, try alternative method
                const newTab = window.open();
                if (newTab) {
                    newTab.document.write(printHtml);
                    newTab.document.close();
                    setTimeout(() => {
                        newTab.print();
                    }, 500);
                } else {
                    // If all else fails, create a new tab with the print HTML
                    const blob = new Blob([printHtml], {type: 'text/html'});
                    const url = URL.createObjectURL(blob);
                    window.open(url);
                }
            } else {
                printWindow.document.write(printHtml);
                printWindow.document.close();
                
                // Wait for content to load, then print
                printWindow.onload = function() {
                    setTimeout(() => {
                        printWindow.print();
                        // Optionally close after printing
                        // printWindow.close();
                    }, 250);
                };
            }
            
            // Mark bill as printed
            const response = await fetch((this.basePath || '') + '/api/bills/' + billId + '/mark-printed', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + this.token,
                    'Content-Type': 'application/json'
                }
            });
            
            if (!response.ok) {
                console.warn('Failed to mark bill as printed:', response.status);
            }
            
            return true;
        } catch (error) {
            console.error('PDF printing error:', error);
            throw error;
        }
    }
    
    generatePrintHTML(printData) {
        const { bill, items, shop } = printData;
        const date = new Date(bill.created_at).toLocaleString();
        
        let html = `
<!DOCTYPE html>
<html>
<head>
    <title>Bill ${bill.bill_number}</title>
    <style>
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }
            body {
                margin: 0;
                padding: 10px;
                font-family: Arial, sans-serif;
                font-size: 12px;
            }
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 10px;
            max-width: 80mm;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .shop-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .shop-address, .shop-phone {
            font-size: 11px;
            margin: 2px 0;
            color: #333;
        }
        .bill-info {
            margin: 10px 0;
        }
        .bill-info div {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        table th, table td {
            padding: 5px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .total-section {
            margin-top: 10px;
            border-top: 2px solid #000;
            padding-top: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .total-final {
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
        }
        .footer-message {
            margin-bottom: 5px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="shop-name">${shop.name}</div>
        ${shop.address ? `<div class="shop-address">${shop.address}</div>` : ''}
        ${shop.phone ? `<div class="shop-phone">Tel: ${shop.phone}</div>` : ''}
    </div>
    
    <div class="bill-info">
        <div><strong>Bill #:</strong> ${bill.bill_number}</div>
        <div><strong>Date:</strong> ${date}</div>
        <div><strong>Customer:</strong> ${bill.customer_name}</div>
        <div><strong>Staff:</strong> ${bill.staff_name}</div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
`;
        
        items.forEach(item => {
            const itemTotal = (item.unit_price * item.quantity) - item.discount;
            html += `
            <tr>
                <td>${item.item_name}</td>
                <td>${item.quantity}</td>
                <td>Rs. ${parseFloat(item.unit_price).toFixed(2)}</td>
                <td>Rs. ${itemTotal.toFixed(2)}</td>
            </tr>
`;
        });
        
        html += `
        </tbody>
    </table>
    
    <div class="total-section">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>Rs. ${parseFloat(bill.subtotal).toFixed(2)}</span>
        </div>
        <div class="total-row">
            <span>Discount:</span>
            <span>Rs. ${parseFloat(bill.total_discount).toFixed(2)}</span>
        </div>
        <div class="total-row total-final">
            <span>Total:</span>
            <span>Rs. ${parseFloat(bill.total_amount).toFixed(2)}</span>
        </div>
        <div class="total-row">
            <span>Paid:</span>
            <span>Rs. ${parseFloat(bill.paid_amount).toFixed(2)}</span>
        </div>
        <div class="total-row">
            <span>Balance:</span>
            <span>Rs. ${parseFloat(bill.balance).toFixed(2)}</span>
        </div>
    </div>
    
    <div class="footer">
        ${shop.receipt_footer ? `<div class="footer-message">${shop.receipt_footer}</div>` : '<div class="footer-message">Thank you for your business!</div>'}
        <div>${shop.name}</div>
        ${shop.phone ? `<div>${shop.phone}</div>` : ''}
    </div>
</body>
</html>
`;
        
        return html;
    }
    
    async markBillAsPrinted(billId) {
        try {
            // This would update the bill's is_printed status
            const response = await fetch((this.basePath || '') + '/api/bills/' + billId + '/mark-printed', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + this.token,
                    'Content-Type': 'application/json'
                }
            });
            
            if (!response.ok) {
                console.warn('Failed to mark bill as printed:', response.status);
            }
        } catch (error) {
            console.error('Failed to mark bill as printed:', error);
        }
    }
}

// Global printer instance
let printerHandler = null;

// Initialize printer on page load
document.addEventListener('DOMContentLoaded', function() {
    printerHandler = new PrinterHandler();
});

// PDF printing fallback function
async function printBillAsPDF(billId, printData = null) {
    try {
        // If no print data provided, fetch it
        if (!printData) {
            const response = await fetch(((window.BASE_PATH && window.BASE_PATH !== '/') ? window.BASE_PATH : (window.location.pathname.match(/^\/[^\/]+/) || [''])[0] || '') + '/api/printers/bill/' + billId, {
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                    'Content-Type': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Failed to load bill data');
            }
            printData = data.data;
        }
        
        // Generate print-ready HTML
        const printHtml = printerHandler.generatePrintHTML(printData);
        
        // Open PDF viewer in new window
        const printWindow = window.open('', '_blank');
        printWindow.document.write(printHtml);
        printWindow.document.close();
        
        // Wait for content to load, then print
        printWindow.onload = function() {
            setTimeout(() => {
                printWindow.print();
                // Optionally close after printing
                // printWindow.close();
            }, 250);
        };
        
        // Mark bill as printed
        const response = await fetch(((window.BASE_PATH && window.BASE_PATH !== '/') ? window.BASE_PATH : (window.location.pathname.match(/^\/[^\/]+/) || [''])[0] || '') + '/api/bills/' + billId + '/mark-printed', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Content-Type': 'application/json'
            }
        });
        
        if (!response.ok) {
            console.warn('Failed to mark bill as printed:', response.status);
        }
        
        return true;
    } catch (error) {
        console.error('PDF printing error:', error);
        throw error;
    }
}

// Utility function to get printer status
function getPrinterStatus() {
    if (printerHandler && printerHandler.isConnected !== undefined) {
        return printerHandler.isConnected ? 'Connected' : 'Not Connected';
    }
    return 'Not Connected';
}

// Utility function to check if printer is available
function isPrinterAvailable() {
    return printerHandler && printerHandler.isConnected === true;
}

// Enhanced printBill function that can be called globally
async function printBill(billId, printData = null) {
    if (printerHandler) {
        try {
            return await printerHandler.printBill(billId, printData);
        } catch (error) {
            console.error('Global printBill error:', error);
            // Fallback to PDF printing
            return await printBillAsPDF(billId, printData);
        }
    } else {
        // Fallback to PDF printing
        return await printBillAsPDF(billId, printData);
    }
}

