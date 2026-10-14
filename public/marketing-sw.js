/* WORKOM marketing PWA — static assets + marketing pages only */
const CACHE_VERSION = 'workom-marketing-v2';
const PRECACHE_URLS = [
    '/',
    '/produkti',
    '/produkti/erp',
    '/produkti/crm',
    '/produkti/hr',
    '/faq',
    '/kontakt',
    '/assets/marketing.css',
    '/favicon.png',
    '/images/marketing/workom_logo_dark.png',
    '/images/marketing/workom_logo.png',
    '/icons/marketing/icon-192.png',
    '/icons/marketing/icon-512.png',
    '/marketing.webmanifest',
];

const MARKETING_PATHS = [
    /^\/$/,
    /^\/produkti(?:\/|$)/,
    /^\/faq$/,
    /^\/kontakt$/,
    /^\/privacy-policy(?:\/(?:sq|en|de))?$/,
    /^\/locale\/[a-z]{2}$/,
];

function isMarketingPath(pathname) {
    return MARKETING_PATHS.some(function (pattern) {
        return pattern.test(pathname);
    });
}

function isStaticAsset(pathname) {
    return (
        pathname.startsWith('/assets/') ||
        pathname.startsWith('/images/marketing/') ||
        pathname.startsWith('/icons/marketing/') ||
        pathname === '/favicon.png' ||
        pathname === '/marketing.webmanifest'
    );
}

self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(CACHE_VERSION).then(function (cache) {
            return cache.addAll(PRECACHE_URLS);
        }).then(function () {
            return self.skipWaiting();
        })
    );
});

self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(function (keys) {
            return Promise.all(
                keys
                    .filter(function (key) { return key.startsWith('workom-marketing-') && key !== CACHE_VERSION; })
                    .map(function (key) { return caches.delete(key); })
            );
        }).then(function () {
            return self.clients.claim();
        })
    );
});

self.addEventListener('fetch', function (event) {
    if (event.request.method !== 'GET') return;

    var url = new URL(event.request.url);
    if (url.origin !== self.location.origin) return;

    if (!isMarketingPath(url.pathname) && !isStaticAsset(url.pathname)) {
        return;
    }

    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .then(function (response) {
                    if (response && response.ok) {
                        var copy = response.clone();
                        caches.open(CACHE_VERSION).then(function (cache) {
                            cache.put(event.request, copy);
                        });
                    }
                    return response;
                })
                .catch(function () {
                    return caches.match(event.request).then(function (cached) {
                        if (cached) return cached;
                        return caches.match('/');
                    });
                })
        );
        return;
    }

    event.respondWith(
        caches.match(event.request).then(function (cached) {
            if (cached) return cached;
            return fetch(event.request).then(function (response) {
                if (!response || response.status !== 200 || response.type === 'opaque') {
                    return response;
                }
                var copy = response.clone();
                caches.open(CACHE_VERSION).then(function (cache) {
                    cache.put(event.request, copy);
                });
                return response;
            });
        })
    );
});
