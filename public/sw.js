const CACHE_NAME = 'version-1';
const assets = ['/pwa/icons/ios/192.png']; // Añade aquí tus rutas de assets

// Evento de Instalación: Guarda en caché archivos estáticos
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(assets))
    );
});

// Evento de Activación: Limpia cachés antiguas
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => Promise.all(
            keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
        ))
    );
});

// Evento Fetch: Intercepta peticiones y sirve desde caché si existe
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request);
        })
    );
});