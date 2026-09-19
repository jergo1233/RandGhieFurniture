self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open('gu-store-cache-v2').then((cache) => {
      return cache.addAll([
        '/',
        '/index.php',
        '/main.js',
        '/disck_data.js',
        '/manifest.json'
      ]);
    })
  );
});

self.addEventListener('fetch', (e) => {
  e.respondWith(
    caches.match(e.request).then((response) => {
      return response || fetch(e.request);
    })
  );
});
