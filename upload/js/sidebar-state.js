/**
 * Sidebar State Persistence
 * Remembers sidebar open/closed state across page refreshes using localStorage
 */

(function() {
    'use strict';
    
    // Storage key for sidebar state
    const SIDEBAR_STATE_KEY = 'chivalry_sidebar_state';
    
    // Get saved state from localStorage
    function getSavedState() {
        const saved = localStorage.getItem(SIDEBAR_STATE_KEY);
        return saved || 'closed'; // Default to closed
    }
    
    // Save state to localStorage
    function saveState(state) {
        localStorage.setItem(SIDEBAR_STATE_KEY, state);
    }
    
    // Apply saved state on page load
    function applySavedState() {
        const state = getSavedState();
        const pageWrapper = document.querySelector('.page-wrapper');
        const body = document.body;
        
        if (state === 'open') {
            // Open sidebar
            if (pageWrapper) {
                pageWrapper.classList.add('toggled');
            }
            body.classList.add('sidebar-open');
        } else {
            // Close sidebar (default)
            if (pageWrapper) {
                pageWrapper.classList.remove('toggled');
            }
            body.classList.remove('sidebar-open');
        }
    }
    
    // Initialize on DOM ready
    function init() {
        // Apply saved state immediately
        applySavedState();
        
        // Hook into existing toggle functionality
        const toggleBtn = document.getElementById('show-sidebar');
        const closeBtn = document.getElementById('close-sidebar');
        
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                // Toggle state
                const pageWrapper = document.querySelector('.page-wrapper');
                const isOpen = pageWrapper && pageWrapper.classList.contains('toggled');
                
                // Save new state
                saveState(isOpen ? 'closed' : 'open');
            });
        }
        
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                // Closing sidebar
                saveState('closed');
            });
        }
        
        // Also hook into jQuery events if jQuery is available
        if (typeof jQuery !== 'undefined') {
            jQuery(document).ready(function($) {
                // Override the toggle button behavior
                $('#show-sidebar, #toggle-sidebar').off('click.persistence').on('click.persistence', function() {
                    setTimeout(function() {
                        const pageWrapper = $('.page-wrapper');
                        const isOpen = pageWrapper.hasClass('toggled');
                        saveState(isOpen ? 'open' : 'closed');
                    }, 10);
                });
                
                $('#close-sidebar').off('click.persistence').on('click.persistence', function() {
                    saveState('closed');
                });
                
                // Handle sidebar dropdown clicks to remember state
                $('.sidebar-dropdown > a').off('click.persistence').on('click.persistence', function() {
                    // Just ensure we save the current sidebar open/closed state
                    setTimeout(function() {
                        const pageWrapper = $('.page-wrapper');
                        const isOpen = pageWrapper.hasClass('toggled');
                        saveState(isOpen ? 'open' : 'closed');
                    }, 10);
                });
            });
        }
    }
    
    // Run initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        // DOM already loaded
        init();
    }
    
    // Also initialize when jQuery is ready (for compatibility)
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ready(init);
    }
})();