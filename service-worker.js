const CACHE_NAME = 'automed-v1';
const CORE_ASSETS = [
  '/',
  '/index.php',
  '/manifest.webmanifest',
  '/src/css/admin.css',
  '/src/js/admin.js',
  '/src/js/form.js',
  '/src/img/smart-medicine-logo.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(CORE_ASSETS);
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => Promise.all(
      keys.map((key) => {
        if (key !== CACHE_NAME) return caches.delete(key);
      })
    ))
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  const req = event.request;
  // ignore non-GET requests
  if (req.method !== 'GET') return;

  event.respondWith(
    caches.match(req).then((cached) => {
      if (cached) return cached;
      return fetch(req).then((res) => {
        // optionally cache new requests
        if (!res || res.status !== 200 || res.type !== 'basic') return res;
        const responseClone = res.clone();
        caches.open(CACHE_NAME).then((cache) => cache.put(req, responseClone));
        return res;
      }).catch(() => caches.match('/index.php'));
    })
  );
});
