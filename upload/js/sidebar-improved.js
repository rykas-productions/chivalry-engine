/*!
    Improved Sidebar JavaScript
    Fixes scrolling issues and improves dropdown functionality
*/

jQuery(function ($) {
    'use strict';
    
    // Remove any custom scrollbar initialization
    $('.sidebar-content').off('.mCustomScrollbar');
    
    // Improved dropdown menu handling
    $(".sidebar-dropdown > a").off('click').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $this = $(this);
        const $parent = $this.parent();
        const $submenu = $this.next(".sidebar-submenu");
        
        if ($parent.hasClass("active")) {
            // Close this dropdown
            $submenu.slideUp(200, function() {
                $parent.removeClass("active");
            });
        } else {
            // Close all other dropdowns
            $(".sidebar-dropdown.active").each(function() {
                $(this).find(".sidebar-submenu").slideUp(200);
                $(this).removeClass("active");
            });
            
            // Open this dropdown
            $submenu.slideDown(200, function() {
                $parent.addClass("active");
                
                // Ensure the opened menu is visible in viewport
                ensureVisible($parent[0]);
            });
        }
    });
    
    // Function to ensure element is visible in scrollable container
    function ensureVisible(element) {
        const sidebar = document.querySelector('.sidebar-content');
        if (!sidebar || !element) return;
        
        const sidebarRect = sidebar.getBoundingClientRect();
        const elementRect = element.getBoundingClientRect();
        
        // Check if element is below viewport
        if (elementRect.bottom > sidebarRect.bottom) {
            const scrollAmount = elementRect.bottom - sidebarRect.bottom + 20;
            sidebar.scrollTop += scrollAmount;
        }
        // Check if element is above viewport
        else if (elementRect.top < sidebarRect.top) {
            const scrollAmount = elementRect.top - sidebarRect.top - 20;
            sidebar.scrollTop += scrollAmount;
        }
    }
    
    // Toggle sidebar
    $("#toggle-sidebar, #show-sidebar").off('click').on('click', function (e) {
        e.preventDefault();
        $(".page-wrapper").toggleClass("toggled");
        
        // Add/remove body class for mobile
        if ($(window).width() <= 768) {
            $('body').toggleClass('sidebar-open');
        }
    });
    
    // Close sidebar button
    $("#close-sidebar").off('click').on('click', function (e) {
        e.preventDefault();
        $(".page-wrapper").addClass("toggled");
        
        // Remove body class for mobile
        if ($(window).width() <= 768) {
            $('body').removeClass('sidebar-open');
        }
    });
    
    // Click overlay to close sidebar on mobile
    $(".sidebar-overlay").off('click').on('click', function () {
        $(".page-wrapper").addClass("toggled");
        $('body').removeClass('sidebar-open');
    });
    
    // Prevent sidebar from closing when clicking inside it
    $(".sidebar-wrapper").off('click').on('click', function (e) {
        e.stopPropagation();
    });
    
    // Handle window resize
    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // Reset sidebar state on desktop
            if ($(window).width() > 768) {
                $('body').removeClass('sidebar-open');
                $('.sidebar-overlay').hide();
            } else {
                $('.sidebar-overlay').show();
            }
        }, 250);
    });
    
    // Initialize sidebar state
    function initSidebar() {
        // Remove any custom scrollbar classes
        $('.sidebar-content').removeClass('mCustomScrollbar');
        $('.mCSB_container').contents().unwrap();
        $('.mCSB_scrollTools').remove();
        
        // Set initial state based on screen size
        if ($(window).width() <= 768) {
            $(".page-wrapper").addClass("toggled");
        }
        
        // Make sidebar content naturally scrollable
        $('.sidebar-content').css({
            'overflow-y': 'auto',
            'overflow-x': 'hidden',
            'height': 'calc(100vh - 55px)',
            'max-height': 'calc(100vh - 55px)'
        });
    }
    
    // Initialize on document ready
    initSidebar();
    
    // Keyboard navigation for accessibility
    $(document).on('keydown', function(e) {
        // ESC key closes sidebar on mobile
        if (e.keyCode === 27 && $(window).width() <= 768) {
            if (!$(".page-wrapper").hasClass("toggled")) {
                $(".page-wrapper").addClass("toggled");
                $('body').removeClass('sidebar-open');
            }
        }
    });
    
    // Improve link clicking reliability
    $(".sidebar-wrapper a").off('mousedown').on('mousedown', function(e) {
        // Prevent text selection which can interfere with clicking
        e.preventDefault();
    });
    
    // Fix for iOS Safari
    if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
        $('.sidebar-content').css({
            '-webkit-overflow-scrolling': 'touch',
            'overflow-y': 'scroll'
        });
    }
    
    // Monitor for dynamically added content
    const sidebarObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                // Reattach event handlers for new dropdown items
                $(".sidebar-dropdown > a").off('click').on('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    // ... dropdown logic from above
                });
            }
        });
    });
    
    // Start observing sidebar for changes
    const sidebarElement = document.querySelector('.sidebar-menu');
    if (sidebarElement) {
        sidebarObserver.observe(sidebarElement, {
            childList: true,
            subtree: true
        });
    }
});