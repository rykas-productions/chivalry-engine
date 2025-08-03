// Modern JavaScript Enhancements for Chivalry Engine

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeEnhancements();
    initializeAjaxSystem();
    initializeNotifications();
    initializeAnimations();
    initializeThemeToggle();
    initializeBootstrap5Features();
});

// Main initialization function
function initializeEnhancements() {
    // Enable tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Enable popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// AJAX System for dynamic updates
function initializeAjaxSystem() {
    // Auto-refresh stats every 30 seconds
    setInterval(refreshStats, 30000);
    
    // AJAX form submissions
    document.querySelectorAll('form.ajax-form').forEach(form => {
        form.addEventListener('submit', handleAjaxForm);
    });
}

// Refresh player stats without page reload
async function refreshStats() {
    try {
        const response = await fetch('api/get_stats.php');
        const data = await response.json();
        
        if (data.success) {
            updateStatDisplays(data.stats);
            showToast('Stats refreshed', 'success');
        }
    } catch (error) {
        console.error('Failed to refresh stats:', error);
    }
}

// Update stat displays with animation
function updateStatDisplays(stats) {
    // Update progress bars with animation
    updateProgressBar('hp-bar', stats.hp_percent);
    updateProgressBar('energy-bar', stats.energy_percent);
    updateProgressBar('xp-bar', stats.xp_percent);
    updateProgressBar('will-bar', stats.will_percent);
    updateProgressBar('brave-bar', stats.brave_percent);
    
    // Update currency with counting animation
    animateValue('primary-currency', stats.primary_currency);
    animateValue('secondary-currency', stats.secondary_currency);
}

// Animate progress bar updates
function updateProgressBar(id, newValue) {
    const bar = document.getElementById(id);
    if (bar) {
        const currentWidth = parseInt(bar.style.width) || 0;
        bar.style.width = newValue + '%';
        bar.textContent = newValue + '%';
        
        // Add pulse animation if value increased
        if (newValue > currentWidth) {
            bar.classList.add('pulse-animation');
            setTimeout(() => bar.classList.remove('pulse-animation'), 600);
        }
    }
}

// Animate number changes
function animateValue(id, end, duration = 500) {
    const element = document.getElementById(id);
    if (!element) return;
    
    const start = parseInt(element.textContent.replace(/,/g, '')) || 0;
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            element.textContent = numberWithCommas(end);
            clearInterval(timer);
        } else {
            element.textContent = numberWithCommas(Math.floor(current));
        }
    }, 16);
}

// Format numbers with commas
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Handle AJAX form submissions
async function handleAjaxForm(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('[type="submit"]');
    
    // Disable submit button and show loading
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    }
    
    try {
        const response = await fetch(form.action, {
            method: form.method || 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(data.message || 'Success!', 'success');
            if (data.redirect) {
                setTimeout(() => window.location.href = data.redirect, 1000);
            }
            if (data.refresh) {
                refreshStats();
            }
        } else {
            showToast(data.message || 'An error occurred', 'error');
        }
    } catch (error) {
        showToast('Network error. Please try again.', 'error');
        console.error('Form submission error:', error);
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = submitBtn.getAttribute('data-original-text') || 'Submit';
        }
    }
}

// Modern notification system
function initializeNotifications() {
    // Check for new notifications every minute
    setInterval(checkNotifications, 60000);
    
    // Request notification permission
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}

// Check for new notifications
async function checkNotifications() {
    try {
        const response = await fetch('api/check_notifications.php');
        const data = await response.json();
        
        if (data.new_notifications > 0) {
            updateNotificationBadge(data.new_notifications);
            
            // Show browser notification if permitted
            if (Notification.permission === 'granted') {
                new Notification('New Notification!', {
                    body: `You have ${data.new_notifications} new notification(s)`,
                    icon: '/images/notification-icon.png'
                });
            }
        }
    } catch (error) {
        console.error('Failed to check notifications:', error);
    }
}

// Update notification badge
function updateNotificationBadge(count) {
    const badges = document.querySelectorAll('.notification-badge');
    badges.forEach(badge => {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline-block' : 'none';
        
        // Add pulse animation for new notifications
        if (count > 0) {
            badge.classList.add('pulse');
            setTimeout(() => badge.classList.remove('pulse'), 2000);
        }
    });
}

// Toast notification system
function showToast(message, type = 'info', duration = 3000) {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type} animate__animated animate__fadeInRight`;
    
    const icon = getToastIcon(type);
    toast.innerHTML = `
        <div class="toast-content">
            <i class="${icon}"></i>
            <span>${message}</span>
        </div>
        <button class="toast-close">&times;</button>
    `;
    
    toastContainer.appendChild(toast);
    
    // Auto-remove after duration
    setTimeout(() => {
        toast.classList.add('animate__fadeOutRight');
        setTimeout(() => toast.remove(), 500);
    }, duration);
    
    // Manual close
    toast.querySelector('.toast-close').addEventListener('click', () => {
        toast.classList.add('animate__fadeOutRight');
        setTimeout(() => toast.remove(), 500);
    });
}

// Create toast container if it doesn't exist
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
    `;
    document.body.appendChild(container);
    return container;
}

// Get appropriate icon for toast type
function getToastIcon(type) {
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };
    return icons[type] || icons.info;
}

// Initialize animations
function initializeAnimations() {
    // Intersection Observer for scroll animations
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    // Observe elements with scroll-animation class
    document.querySelectorAll('.scroll-animation').forEach(el => {
        observer.observe(el);
    });
    
    // Add hover effects to cards
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-5px)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
        });
    });
}

// Theme toggle functionality
function initializeThemeToggle() {
    const themeToggle = document.getElementById('theme-toggle');
    if (!themeToggle) return;
    
    // Load saved theme
    const savedTheme = localStorage.getItem('theme') || 'dark';
    document.body.setAttribute('data-theme', savedTheme);
    
    themeToggle.addEventListener('click', () => {
        const currentTheme = document.body.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.body.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        // Animate theme change
        document.body.style.transition = 'all 0.3s ease';
        
        showToast(`Switched to ${newTheme} theme`, 'success');
    });
}

// Add loading spinner to buttons
document.querySelectorAll('[data-loading]').forEach(button => {
    button.addEventListener('click', function() {
        const originalText = this.innerHTML;
        this.setAttribute('data-original-text', originalText);
        this.disabled = true;
        this.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"></span>
            ${this.getAttribute('data-loading') || 'Loading...'}
        `;
    });
});

// Enhanced form validation
document.querySelectorAll('form.needs-validation').forEach(form => {
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K for quick search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('quick-search')?.focus();
    }
    
    // ESC to close modals
    if (e.key === 'Escape') {
        const modal = document.querySelector('.modal.show');
        if (modal) {
            bootstrap.Modal.getInstance(modal)?.hide();
        }
    }
});

// Add CSS for toast notifications
const toastStyles = document.createElement('style');
toastStyles.textContent = `
    .toast-notification {
        background: var(--bg-secondary);
        border-left: 4px solid;
        border-radius: 8px;
        padding: 12px 16px;
        min-width: 300px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .toast-content {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .toast-success {
        border-color: var(--success-color);
        color: var(--success-color);
    }
    
    .toast-error {
        border-color: var(--danger-color);
        color: var(--danger-color);
    }
    
    .toast-warning {
        border-color: var(--warning-color);
        color: var(--warning-color);
    }
    
    .toast-info {
        border-color: var(--info-color);
        color: var(--info-color);
    }
    
    .toast-close {
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 20px;
        cursor: pointer;
        padding: 0;
        margin-left: 10px;
    }
    
    .pulse {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
        }
    }
    
    .pulse-animation {
        animation: pulse-bg 0.6s ease;
    }
    
    @keyframes pulse-bg {
        0% {
            box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(99, 102, 241, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(99, 102, 241, 0);
        }
    }
`;
document.head.appendChild(toastStyles);

// Bootstrap 5.x Features Implementation
function initializeBootstrap5Features() {
    // Initialize Bootstrap 5 Toasts (better than our custom ones)
    initializeBootstrapToasts();
    
    // Initialize Offcanvas
    initializeOffcanvas();
    
    // Initialize Floating Labels
    initializeFloatingLabels();
    
    // Initialize Placeholders/Skeletons
    initializePlaceholders();
    
    // Initialize enhanced Popovers with dismiss behavior
    initializeEnhancedPopovers();
    
    // Initialize Accordion with icons
    initializeAccordions();
}

// Bootstrap 5 Toast System (replaces custom toast)
function initializeBootstrapToasts() {
    // Override the existing showToast function to use Bootstrap 5 toasts
    window.showBootstrapToast = function(title, message, type = 'info', delay = 5000) {
        const toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) return;
        
        const toastId = 'toast-' + Date.now();
        const iconMap = {
            success: 'fas fa-check-circle text-success',
            danger: 'fas fa-exclamation-circle text-danger',
            warning: 'fas fa-exclamation-triangle text-warning',
            info: 'fas fa-info-circle text-info'
        };
        
        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="${delay}">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="${iconMap[type] || iconMap.info} me-2"></i>
                        <strong>${title}</strong> ${message}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
        
        // Clean up after hiding
        toastElement.addEventListener('hidden.bs.toast', function() {
            toastElement.remove();
        });
        
        return toast;
    };
}

// Enhanced Offcanvas functionality
function initializeOffcanvas() {
    const offcanvasElement = document.getElementById('gameOffcanvas');
    if (offcanvasElement) {
        const offcanvas = new bootstrap.Offcanvas(offcanvasElement);
        
        // Auto-close on mobile after navigation
        offcanvasElement.querySelectorAll('a[href]:not([href="#"])').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    setTimeout(() => offcanvas.hide(), 100);
                }
            });
        });
        
        // Keyboard navigation
        offcanvasElement.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                offcanvas.hide();
            }
        });
    }
}

// Floating Labels enhancements
function initializeFloatingLabels() {
    document.querySelectorAll('.form-floating').forEach(floatingDiv => {
        const input = floatingDiv.querySelector('input, textarea, select');
        const label = floatingDiv.querySelector('label');
        
        if (input && label) {
            // Enhanced animation for floating labels
            const updateLabelState = () => {
                if (input.value.length > 0 || document.activeElement === input) {
                    floatingDiv.classList.add('focused');
                } else {
                    floatingDiv.classList.remove('focused');
                }
            };
            
            input.addEventListener('input', updateLabelState);
            input.addEventListener('focus', updateLabelState);
            input.addEventListener('blur', updateLabelState);
            updateLabelState(); // Initial state
        }
    });
}

// Placeholder/Skeleton loading system
function initializePlaceholders() {
    // Function to show skeleton loading
    window.showSkeleton = function(element, lines = 3) {
        const originalContent = element.innerHTML;
        element.setAttribute('data-original-content', originalContent);
        
        let skeletonHTML = '<div class="placeholder-glow">';
        for (let i = 0; i < lines; i++) {
            const width = Math.random() * 40 + 60; // Random width between 60-100%
            skeletonHTML += `<span class="placeholder col-${Math.floor(width/10)} mb-1"></span><br>`;
        }
        skeletonHTML += '</div>';
        
        element.innerHTML = skeletonHTML;
    };
    
    // Function to hide skeleton loading
    window.hideSkeleton = function(element) {
        const originalContent = element.getAttribute('data-original-content');
        if (originalContent) {
            element.innerHTML = originalContent;
            element.removeAttribute('data-original-content');
        }
    };
    
    // Auto-skeleton for loading states
    document.querySelectorAll('[data-loading-skeleton]').forEach(element => {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'data-loading') {
                    if (element.hasAttribute('data-loading')) {
                        showSkeleton(element);
                    } else {
                        hideSkeleton(element);
                    }
                }
            });
        });
        
        observer.observe(element, { attributes: true });
    });
}

// Enhanced Popovers with dismiss behavior
function initializeEnhancedPopovers() {
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(popoverTrigger => {
        const popover = new bootstrap.Popover(popoverTrigger, {
            container: 'body',
            trigger: 'click',
            placement: 'auto',
            html: true
        });
        
        // Auto-dismiss when clicking outside
        document.addEventListener('click', function(e) {
            if (!popoverTrigger.contains(e.target)) {
                popover.hide();
            }
        });
    });
}

// Accordion with icons
function initializeAccordions() {
    document.querySelectorAll('.accordion').forEach(accordion => {
        accordion.addEventListener('show.bs.collapse', function(e) {
            const button = e.target.previousElementSibling.querySelector('.accordion-button');
            if (button) {
                const icon = button.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-plus');
                    icon.classList.add('fa-minus');
                }
            }
        });
        
        accordion.addEventListener('hide.bs.collapse', function(e) {
            const button = e.target.previousElementSibling.querySelector('.accordion-button');
            if (button) {
                const icon = button.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-minus');
                    icon.classList.add('fa-plus');
                }
            }
        });
    });
}

// Enhanced button loading states (Bootstrap 5 style)
window.setBootstrapButtonLoading = function(button, loading = true, text = 'Loading...') {
    if (loading) {
        button.disabled = true;
        button.setAttribute('data-original-html', button.innerHTML);
        button.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            ${text}
        `;
    } else {
        button.disabled = false;
        const originalHtml = button.getAttribute('data-original-html');
        if (originalHtml) {
            button.innerHTML = originalHtml;
        }
    }
};

// Validation feedback enhancement
document.querySelectorAll('.needs-validation').forEach(form => {
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            
            // Focus first invalid field
            const firstInvalid = form.querySelector(':invalid');
            if (firstInvalid) {
                firstInvalid.focus();
                showBootstrapToast('Validation Error', 'Please check the form for errors', 'danger');
            }
        }
        form.classList.add('was-validated');
    }, false);
});

// Progressive enhancement for cards
document.querySelectorAll('.card').forEach(card => {
    // Add ripple effect on click
    card.addEventListener('click', function(e) {
        if (card.classList.contains('card-clickable')) {
            createRippleEffect(e, card);
        }
    });
});

// Ripple effect function
function createRippleEffect(event, element) {
    const ripple = document.createElement('span');
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
    `;
    
    element.style.position = 'relative';
    element.style.overflow = 'hidden';
    element.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

// Add ripple animation CSS
const rippleStyles = document.createElement('style');
rippleStyles.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .form-floating.focused label {
        color: var(--primary-color);
    }
    
    .form-floating.focused .form-control {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-color), 0.25);
    }
`;
document.head.appendChild(rippleStyles);

// ========================================
// WOW FACTOR IMPLEMENTATIONS
// ========================================

// Combat Animation System
window.triggerCombatAnimation = function(element, damage, isCritical = false) {
    // Add combat slash effect
    element.classList.add('combat-slash');
    setTimeout(() => element.classList.remove('combat-slash'), 800);
    
    // Add hit impact
    element.classList.add(isCritical ? 'critical-hit' : 'hit-impact');
    setTimeout(() => {
        element.classList.remove('critical-hit', 'hit-impact');
    }, isCritical ? 600 : 400);
    
    // Show damage number
    showDamageNumber(element, damage, isCritical);
    
    // Create particle effects
    if (isCritical) {
        createParticleExplosion(element, 'gold', 15);
    } else {
        createParticleExplosion(element, '#ff4444', 8);
    }
    
    // Screen shake for critical hits
    if (isCritical) {
        screenShake();
    }
};

// Show floating damage numbers
function showDamageNumber(element, damage, isCritical = false) {
    const rect = element.getBoundingClientRect();
    const damageEl = document.createElement('div');
    damageEl.className = 'damage-number';
    damageEl.textContent = `-${damage}`;
    
    if (isCritical) {
        damageEl.style.color = '#ffaa00';
        damageEl.style.fontSize = '2rem';
        damageEl.textContent = `CRIT! -${damage}`;
    }
    
    damageEl.style.left = rect.left + rect.width / 2 + 'px';
    damageEl.style.top = rect.top + 'px';
    
    document.body.appendChild(damageEl);
    
    setTimeout(() => damageEl.remove(), 1500);
}

// Particle explosion effect
function createParticleExplosion(element, color, count) {
    const rect = element.getBoundingClientRect();
    const centerX = rect.left + rect.width / 2;
    const centerY = rect.top + rect.height / 2;
    
    for (let i = 0; i < count; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.background = color;
        particle.style.left = centerX + (Math.random() - 0.5) * 40 + 'px';
        particle.style.top = centerY + (Math.random() - 0.5) * 40 + 'px';
        
        document.body.appendChild(particle);
        
        setTimeout(() => particle.remove(), 1000);
    }
}

// Screen shake effect
function screenShake() {
    const intensity = 5;
    const duration = 300;
    const originalTransform = document.body.style.transform;
    
    let startTime = null;
    
    function shake(timestamp) {
        if (!startTime) startTime = timestamp;
        const elapsed = timestamp - startTime;
        
        if (elapsed < duration) {
            const x = (Math.random() - 0.5) * intensity;
            const y = (Math.random() - 0.5) * intensity;
            document.body.style.transform = `translate(${x}px, ${y}px)`;
            requestAnimationFrame(shake);
        } else {
            document.body.style.transform = originalTransform;
        }
    }
    
    requestAnimationFrame(shake);
}

// Level Up Animation System
window.triggerLevelUp = function(newLevel) {
    // Create level up overlay
    const overlay = document.createElement('div');
    overlay.className = 'level-up-overlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        animation: fadeIn 0.5s ease-out;
    `;
    
    const levelText = document.createElement('div');
    levelText.className = 'level-up-text';
    levelText.style.cssText = `
        font-size: 4rem;
        text-align: center;
        color: gold;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
    `;
    levelText.innerHTML = `
        <div>🎉 LEVEL UP! 🎉</div>
        <div style="font-size: 2rem; margin-top: 20px;">Level ${newLevel}</div>
    `;
    
    overlay.appendChild(levelText);
    document.body.appendChild(overlay);
    
    // Add glow effect to character stats
    document.querySelectorAll('.stat-item').forEach(stat => {
        stat.classList.add('level-up-glow');
        setTimeout(() => stat.classList.remove('level-up-glow'), 2000);
    });
    
    // Create celebration particles
    createCelebrationParticles();
    
    // Remove overlay after animation
    setTimeout(() => {
        overlay.style.animation = 'fadeOut 0.5s ease-out';
        setTimeout(() => overlay.remove(), 500);
    }, 3000);
    
    // Play level up sound (if available)
    playSound('levelup');
};

// Celebration particle system
function createCelebrationParticles() {
    const particles = ['🎉', '✨', '🌟', '💫', '🎊'];
    
    for (let i = 0; i < 50; i++) {
        setTimeout(() => {
            const particle = document.createElement('div');
            particle.textContent = particles[Math.floor(Math.random() * particles.length)];
            particle.style.cssText = `
                position: fixed;
                font-size: 2rem;
                pointer-events: none;
                z-index: 9999;
                left: ${Math.random() * window.innerWidth}px;
                top: ${window.innerHeight}px;
                animation: floatUp 3s ease-out forwards;
            `;
            
            document.body.appendChild(particle);
            setTimeout(() => particle.remove(), 3000);
        }, i * 50);
    }
}

// Coin Collection Animation
window.animateCoinCollection = function(amount, element) {
    const rect = element ? element.getBoundingClientRect() : { left: window.innerWidth / 2, top: window.innerHeight / 2 };
    
    // Create coin particles
    for (let i = 0; i < Math.min(amount / 10, 20); i++) {
        const coin = document.createElement('div');
        coin.className = 'coin-particle';
        coin.textContent = '💰';
        coin.style.left = rect.left + (Math.random() - 0.5) * 100 + 'px';
        coin.style.top = rect.top + (Math.random() - 0.5) * 50 + 'px';
        
        document.body.appendChild(coin);
        setTimeout(() => coin.remove(), 2000);
    }
    
    // Update currency display with animation
    const currencyDisplay = document.getElementById('primary-currency');
    if (currencyDisplay) {
        currencyDisplay.parentElement.classList.add('glow-on-hover');
        setTimeout(() => {
            currencyDisplay.parentElement.classList.remove('glow-on-hover');
        }, 1000);
    }
    
    playSound('coin');
};

// Health Bar Animation System
window.animateHealthChange = function(healthBar, oldValue, newValue, isHealing = false) {
    const bar = document.querySelector(healthBar);
    if (!bar) return;
    
    // Add animation class
    bar.classList.add(isHealing ? 'health-recover' : 'health-drain');
    
    // Animate width change
    bar.style.transition = 'width 0.8s ease-out';
    bar.style.width = newValue + '%';
    
    // Show heal/damage number
    if (isHealing) {
        showHealNumber(bar.parentElement, Math.abs(newValue - oldValue));
    }
    
    // Remove animation class
    setTimeout(() => {
        bar.classList.remove('health-recover', 'health-drain');
    }, 800);
};

// Show healing numbers
function showHealNumber(element, amount) {
    const rect = element.getBoundingClientRect();
    const healEl = document.createElement('div');
    healEl.className = 'heal-number';
    healEl.textContent = `+${amount}`;
    healEl.style.left = rect.left + rect.width / 2 + 'px';
    healEl.style.top = rect.top + 'px';
    
    document.body.appendChild(healEl);
    setTimeout(() => healEl.remove(), 1500);
}

// Typewriter Effect for Messages
window.typewriterEffect = function(element, text, speed = 50) {
    element.innerHTML = '';
    element.style.borderRight = '2px solid';
    element.style.animation = 'blink 1s infinite';
    
    let i = 0;
    const timer = setInterval(() => {
        element.innerHTML += text.charAt(i);
        i++;
        
        if (i >= text.length) {
            clearInterval(timer);
            element.style.borderRight = 'none';
            element.style.animation = 'none';
        }
    }, speed);
};

// Progressive Loading with Skeleton
window.enhancedLoader = function(container, loadFunction, duration = 2000) {
    // Show skeleton
    showSkeleton(container, 5);
    
    // Simulate progressive loading
    setTimeout(() => {
        loadFunction();
        hideSkeleton(container);
        
        // Add entrance animation
        container.style.animation = 'fadeInUp 0.6s ease-out';
    }, duration);
};

// Sound System (if audio files available)
function playSound(soundName) {
    try {
        const audio = new Audio(`/sounds/${soundName}.mp3`);
        audio.volume = 0.3;
        audio.play().catch(() => {}); // Ignore errors if audio not available
    } catch (e) {
        // Audio not available, continue silently
    }
}

// Interactive Tutorial System
window.startInteractiveTutorial = function(steps) {
    let currentStep = 0;
    
    function showStep(step) {
        const overlay = document.createElement('div');
        overlay.className = 'tutorial-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        
        const tooltip = document.createElement('div');
        tooltip.style.cssText = `
            background: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        `;
        
        tooltip.innerHTML = `
            <h3>${step.title}</h3>
            <p>${step.description}</p>
            <button class="btn btn-primary" onclick="nextTutorialStep()">
                ${currentStep < steps.length - 1 ? 'Next' : 'Finish'}
            </button>
        `;
        
        overlay.appendChild(tooltip);
        document.body.appendChild(overlay);
        
        // Highlight target element
        if (step.target) {
            const target = document.querySelector(step.target);
            if (target) {
                target.style.boxShadow = '0 0 0 9999px rgba(0,0,0,0.8)';
                target.style.position = 'relative';
                target.style.zIndex = '10001';
            }
        }
        
        window.nextTutorialStep = function() {
            overlay.remove();
            if (step.target) {
                const target = document.querySelector(step.target);
                if (target) {
                    target.style.boxShadow = '';
                    target.style.zIndex = '';
                }
            }
            
            currentStep++;
            if (currentStep < steps.length) {
                setTimeout(() => showStep(steps[currentStep]), 500);
            }
        };
    }
    
    showStep(steps[0]);
};

// Add CSS for wow effects
const wowStyles = document.createElement('style');
wowStyles.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .health-drain {
        animation: healthDrain 1s ease-in-out;
    }
    
    .health-recover {
        animation: healthRecover 1s ease-in-out;
    }
`;
document.head.appendChild(wowStyles);