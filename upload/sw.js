// Service Worker for Chivalry Engine - Performance Optimization
const CACHE_NAME = 'chivalry-engine-v1';
const STATIC_CACHE_URLS = [
    '/',
    '/css/master-combined.css',
    '/css/themes.css',
    '/js/theme-switcher.js',
    '/js/sidebar-state.js',
    '/js/modern-enhancements.js',
    '/js/realtime-stats.js',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'
];

// Install event - cache static resources
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Caching static resources');
                return cache.addAll(STATIC_CACHE_URLS);
            })
            .catch(err => {
                console.log('Cache install failed:', err);
            })
    );
    self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch event - serve from cache when possible
self.addEventListener('fetch', event => {
    // Skip non-HTTP requests
    if (!event.request.url.startsWith('http')) {
        return;
    }

    // Skip API calls and dynamic content
    if (event.request.url.includes('/api/') || 
        event.request.url.includes('.php') ||
        event.request.method !== 'GET') {
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Return cached version if available
                if (response) {
                    return response;
                }

                // Otherwise fetch from network
                return fetch(event.request)
                    .then(response => {
                        // Don't cache non-success responses
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }

                        // Clone the response for caching
                        const responseToCache = response.clone();
                        
                        // Cache static resources
                        if (event.request.url.includes('.css') || 
                            event.request.url.includes('.js') ||
                            event.request.url.includes('.png') ||
                            event.request.url.includes('.jpg') ||
                            event.request.url.includes('.gif')) {
                            
                            caches.open(CACHE_NAME)
                                .then(cache => {
                                    cache.put(event.request, responseToCache);
                                });
                        }

                        return response;
                    })
                    .catch(() => {
                        // Return offline fallback for navigation requests
                        if (event.request.mode === 'navigate') {
                            return caches.match('/offline.html');
                        }
                    });
            })
    );
});

// Background sync for API calls when offline
self.addEventListener('sync', event => {
    if (event.tag === 'stats-sync') {
        event.waitUntil(syncStats());
    }
});

async function syncStats() {
    try {
        // Attempt to sync any queued stat updates
        const response = await fetch('/api/get_stats.php');
        if (response.ok) {
            const data = await response.json();
            // Broadcast updated stats to all clients
            self.clients.matchAll().then(clients => {
                clients.forEach(client => {
                    client.postMessage({
                        type: 'STATS_UPDATED',
                        data: data
                    });
                });
            });
        }
    } catch (error) {
        console.log('Stats sync failed:', error);
    }
}