const CACHE_NAME = 'medicine-dispenser-v1';
const urlsToCache = [
  '/',
  '/index.php',
  '/src/css/signin.css',
  '/src/css/admin.css',
  '/src/js/signin.js',
  '/src/js/admin.js',
  '/src/img/smart-medicine-logo.png'
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