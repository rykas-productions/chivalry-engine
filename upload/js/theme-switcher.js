// Theme Switcher System
(function() {
    'use strict';
    
    // Available themes
    const themes = {
        light: {
            name: 'Light Mode',
            icon: 'fa-sun',
            description: 'Classic light theme'
        },
        dark: {
            name: 'Dark Mode', 
            icon: 'fa-moon',
            description: 'Easy on the eyes in low light'
        },
        contrast: {
            name: 'High Contrast',
            icon: 'fa-universal-access',
            description: 'Enhanced visibility and contrast'
        },
        medieval: {
            name: 'Medieval',
            icon: 'fa-shield-alt',
            description: 'Classic medieval game theme'
        },
        modern: {
            name: 'Modern',
            icon: 'fa-star',
            description: 'Sleek modern interface'
        }
    };
    
    // Get current theme from localStorage or default to dark
    function getCurrentTheme() {
        return localStorage.getItem('theme') || 'dark';
    }
    
    // Apply theme to document
    function applyTheme(themeName) {
        // Bootstrap 5.3 color mode support for basic themes
        if (themeName === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        } else if (themeName === 'light') {
            document.documentElement.setAttribute('data-bs-theme', 'light');
        } else {
            // For custom themes, remove bootstrap theme and use our custom theme
            document.documentElement.removeAttribute('data-bs-theme');
        }
        
        // Set custom theme attribute for all themes
        document.documentElement.setAttribute('data-theme', themeName);
        
        // Apply theme-specific CSS classes
        document.body.className = document.body.className.replace(/theme-\w+/g, '');
        document.body.classList.add(`theme-${themeName}`);
        
        localStorage.setItem('theme', themeName);
        
        // Update theme switcher button if it exists
        updateThemeSwitcherButton(themeName);
        
        // Dispatch event for other components to react
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: themeName }));
    }
    
    // Update theme switcher button appearance
    function updateThemeSwitcherButton(themeName) {
        const button = document.getElementById('theme-switcher-btn');
        if (button) {
            const theme = themes[themeName];
            button.innerHTML = `<i class="fas ${theme.icon}"></i>`;
            button.title = `Current: ${theme.name}`;
        }
    }
    
    // Create theme switcher dropdown
    function createThemeSwitcher() {
        const currentTheme = getCurrentTheme();
        
        // Create container
        const container = document.createElement('div');
        container.className = 'theme-switcher-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        `;
        
        // Create button
        const button = document.createElement('button');
        button.id = 'theme-switcher-btn';
        button.className = 'btn btn-sm btn-secondary';
        button.style.cssText = `
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        `;
        
        // Create dropdown menu
        const dropdown = document.createElement('div');
        dropdown.className = 'theme-dropdown';
        dropdown.style.cssText = `
            position: absolute;
            top: 50px;
            right: 0;
            background: var(--bs-body-bg, #fff);
            border: 1px solid var(--bs-border-color, #dee2e6);
            border-radius: 8px;
            padding: 10px;
            min-width: 200px;
            display: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            color: var(--bs-body-color, #000);
        `;
        
        // Add theme options to dropdown
        Object.entries(themes).forEach(([key, theme]) => {
            const option = document.createElement('div');
            option.className = 'theme-option';
            option.style.cssText = `
                padding: 10px;
                cursor: pointer;
                border-radius: 4px;
                margin-bottom: 5px;
                display: flex;
                align-items: center;
                gap: 10px;
                transition: background 0.2s;
            `;
            
            option.innerHTML = `
                <i class="fas ${theme.icon}" style="width: 20px;"></i>
                <div>
                    <div style="font-weight: 600;">${theme.name}</div>
                    <div style="font-size: 12px; opacity: 0.7;">${theme.description}</div>
                </div>
            `;
            
            // Highlight current theme
            if (key === currentTheme) {
                option.style.background = 'var(--bs-primary-bg-subtle, rgba(13, 110, 253, 0.1))';
            }
            
            // Add hover effect
            option.addEventListener('mouseenter', () => {
                option.style.background = 'var(--bs-primary-bg-subtle, rgba(13, 110, 253, 0.1))';
            });
            
            option.addEventListener('mouseleave', () => {
                if (key !== getCurrentTheme()) {
                    option.style.background = 'transparent';
                }
            });
            
            // Handle click
            option.addEventListener('click', () => {
                applyTheme(key);
                dropdown.style.display = 'none';
                
                // Update highlighting
                dropdown.querySelectorAll('.theme-option').forEach(opt => {
                    opt.style.background = 'transparent';
                });
                option.style.background = 'var(--bs-primary-bg-subtle, rgba(13, 110, 253, 0.1))';
            });
            
            dropdown.appendChild(option);
        });
        
        // Toggle dropdown on button click
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', () => {
            dropdown.style.display = 'none';
        });
        
        dropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });
        
        // Assemble and add to page
        container.appendChild(button);
        container.appendChild(dropdown);
        document.body.appendChild(container);
        
        // Set initial theme
        applyTheme(currentTheme);
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            createThemeSwitcher();
        });
    } else {
        createThemeSwitcher();
    }
    
    // Keyboard shortcut for theme switching (Alt + T)
    document.addEventListener('keydown', (e) => {
        if (e.altKey && e.key === 't') {
            e.preventDefault();
            const themeKeys = ['light', 'dark', 'contrast', 'medieval', 'modern'];
            const current = getCurrentTheme();
            const nextIndex = (themeKeys.indexOf(current) + 1) % themeKeys.length;
            applyTheme(themeKeys[nextIndex]);
        }
    });
    
    // Export for global use
    window.applyTheme = applyTheme;
    window.getCurrentTheme = getCurrentTheme;
})();