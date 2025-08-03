// Lazy Loading and Image Optimization for Chivalry Engine
(function() {
    'use strict';
    
    // Intersection Observer for lazy loading
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                
                // Load the image
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.classList.remove('lazy-loading');
                    img.classList.add('lazy-loaded');
                    
                    // Add fade-in animation
                    img.style.opacity = '0';
                    img.style.transition = 'opacity 0.3s ease';
                    
                    img.onload = () => {
                        img.style.opacity = '1';
                    };
                    
                    // Stop observing this image
                    observer.unobserve(img);
                }
            }
        });
    }, {
        rootMargin: '50px 0px',
        threshold: 0.01
    });
    
    // Initialize lazy loading
    function initLazyLoading() {
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => {
            img.classList.add('lazy-loading');
            imageObserver.observe(img);
        });
        
        console.log(`Initialized lazy loading for ${lazyImages.length} images`);
    }
    
    // Optimize existing images
    function optimizeImages() {
        const images = document.querySelectorAll('img:not([data-src])');
        images.forEach(img => {
            // Add loading="lazy" for native lazy loading fallback
            if (!img.hasAttribute('loading')) {
                img.loading = 'lazy';
            }
            
            // Add decoding="async" for better performance
            img.decoding = 'async';
            
            // Add error handling
            img.addEventListener('error', () => {
                img.style.display = 'none';
                console.warn('Failed to load image:', img.src);
            });
        });
    }
    
    // Preload critical images
    function preloadCriticalImages() {
        const criticalImages = [
            // Add any critical images that should load immediately
            // Example: '/images/logo.png',
            // Example: '/images/avatar-placeholder.png'
        ];
        
        criticalImages.forEach(src => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = src;
            document.head.appendChild(link);
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initLazyLoading();
            optimizeImages();
            preloadCriticalImages();
        });
    } else {
        initLazyLoading();
        optimizeImages();
        preloadCriticalImages();
    }
    
    // Re-initialize when new content is added dynamically
    const contentObserver = new MutationObserver(mutations => {
        let shouldReinit = false;
        
        mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType === Node.ELEMENT_NODE) {
                    if (node.tagName === 'IMG' || node.querySelector('img')) {
                        shouldReinit = true;
                    }
                }
            });
        });
        
        if (shouldReinit) {
            setTimeout(() => {
                initLazyLoading();
                optimizeImages();
            }, 100);
        }
    });
    
    contentObserver.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    // Export for manual initialization
    window.ChivalryLazyLoading = {
        init: initLazyLoading,
        optimize: optimizeImages,
        preload: preloadCriticalImages
    };
})();

// CSS for lazy loading animations
const lazyLoadingCSS = document.createElement('style');
lazyLoadingCSS.textContent = `
    .lazy-loading {
        opacity: 0.6;
        filter: blur(2px);
        transition: all 0.3s ease;
    }
    
    .lazy-loaded {
        opacity: 1;
        filter: none;
    }
    
    img[data-src] {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }
    
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    
    /* Dark theme shimmer */
    [data-bs-theme="dark"] img[data-src],
    .theme-dark img[data-src] {
        background: linear-gradient(90deg, #2a2a2a 25%, #3a3a3a 50%, #2a2a2a 75%);
        background-size: 200% 100%;
    }
`;
document.head.appendChild(lazyLoadingCSS);