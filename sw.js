const CACHE_NAME = 'medicine-dispenser-v1';
const urlsToCache = [
  '/medicine-dispenser/',
  '/medicine-dispenser/index.php',
  '/medicine-dispenser/src/css/signin.css',
  '/medicine-dispenser/src/css/admin.css',
  '/medicine-dispenser/src/js/signin.js',
  '/medicine-dispenser/src/js/admin.js',
  '/medicine-dispenser/src/img/smart-medicine-logo.png',
  '/medicine-dispenser/src/img/background.jpg'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        if (response) {
          return response;
        }
        return fetch(event.request);
      }
    )
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});