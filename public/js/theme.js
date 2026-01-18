/**
 * Theme Management System - System-wide theme handling
 */

const ThemeManager = {
    // Initialize theme from localStorage or default to light
    init: function() {
        const savedTheme = localStorage.getItem('theme_mode') || 'light';
        this.applyTheme(savedTheme);
        this.updateToggleButton(savedTheme);
    },

    // Apply theme to the document
    applyTheme: function(theme) {
        const isDark = theme === 'dark';
        
        if (isDark) {
            document.documentElement.classList.add('dark');
            document.body.classList.add('dark', 'bg-gray-900', 'text-gray-100');
            document.body.classList.remove('bg-gray-100', 'text-gray-900');
            
            // Apply to specific elements
            document.querySelectorAll('.bg-white').forEach(el => {
                el.classList.add('dark:bg-gray-800');
            });
            document.querySelectorAll('.bg-gray-50').forEach(el => {
                el.classList.add('dark:bg-gray-700');
            });
            document.querySelectorAll('.text-gray-800, .text-gray-700, .text-gray-600, .text-gray-500').forEach(el => {
                el.classList.add('dark:text-gray-300');
            });
            document.querySelectorAll('.border-gray-300, .border-gray-200').forEach(el => {
                el.classList.add('dark:border-gray-600');
            });
        } else {
            document.documentElement.classList.remove('dark');
            document.body.classList.remove('dark', 'bg-gray-900', 'text-gray-100');
            document.body.classList.add('bg-gray-100');
            
            // Remove dark mode classes
            document.querySelectorAll('.dark\\:bg-gray-800').forEach(el => {
                el.classList.remove('dark:bg-gray-800');
            });
            document.querySelectorAll('.dark\\:bg-gray-700').forEach(el => {
                el.classList.remove('dark:bg-gray-700');
            });
            document.querySelectorAll('.dark\\:text-gray-300').forEach(el => {
                el.classList.remove('dark:text-gray-300');
            });
            document.querySelectorAll('.dark\\:border-gray-600').forEach(el => {
                el.classList.remove('dark:border-gray-600');
            });
        }
        
        localStorage.setItem('theme_mode', theme);
    },

    // Toggle between light and dark theme
    toggle: function() {
        const currentTheme = localStorage.getItem('theme_mode') || 'light';
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        this.applyTheme(newTheme);
        this.updateToggleButton(newTheme);
        return newTheme;
    },

    // Update toggle button icon
    updateToggleButton: function(theme) {
        const isDark = theme === 'dark';
        const sunIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>';
        const moonIcon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>';
        
        // Update primary theme toggle button
        const primaryBtn = document.getElementById('themeToggleBtn');
        if (primaryBtn) {
            primaryBtn.innerHTML = isDark ? moonIcon : sunIcon;
            primaryBtn.title = isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode';
        }
        
        // Update mobile theme toggle button
        const mobileBtn = document.getElementById('themeToggleBtnMobile');
        if (mobileBtn) {
            mobileBtn.innerHTML = isDark ? moonIcon : sunIcon;
            mobileBtn.title = isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode';
        }
    },

    // Get current theme
    getTheme: function() {
        return localStorage.getItem('theme_mode') || 'light';
    }
};

/**
 * Responsive Navigation - Mobile menu handling
 */
const Navigation = {
    init: function() {
        // Mobile menu toggle
        const menuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        
        if (menuBtn && mobileMenu) {
            menuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
    }
};

/**
 * Role-Based UI - Show/hide elements based on user role
 */
const RoleUI = {
    init: function() {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        const role = user.role || 'cashier';
        
        // Role hierarchy: cashier < supervisor < admin
        const roleLevel = { 'cashier': 1, 'supervisor': 2, 'admin': 3 };
        const userLevel = roleLevel[role] || 1;
        
        // Hide elements that require higher permissions
        document.querySelectorAll('[data-require-role]').forEach(el => {
            const requiredRole = el.getAttribute('data-require-role');
            const requiredLevel = roleLevel[requiredRole] || 1;
            
            if (userLevel < requiredLevel) {
                el.classList.add('hidden');
            }
        });
        
        // Show elements based on role
        document.querySelectorAll('[data-show-role]').forEach(el => {
            const showRole = el.getAttribute('data-show-role');
            const showLevel = roleLevel[showRole] || 1;
            
            if (userLevel < showLevel) {
                el.classList.add('hidden');
            }
        });
    }
};

/**
 * Password Change Modal - First-time login password change
 */
const PasswordChangeModal = {
    show: function() {
        const modal = document.getElementById('passwordChangeModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    },
    
    hide: function() {
        const modal = document.getElementById('passwordChangeModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    },
    
    init: function() {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        if (user.must_change_password) {
            this.show();
        }
    }
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    ThemeManager.init();
    Navigation.init();
    RoleUI.init();
});
