/**
 * Service Worker para mejorar la experiencia de la aplicación
 * Incluye estrategias para evitar problemas de caché
 */

// Versión dinámica que cambia cada vez que se actualiza el service worker
const CACHE_VERSION = new Date().getTime().toString();
const CACHE_NAME = 'sac-cache-v' + CACHE_VERSION;

// Recursos a pre-cachear
const INITIAL_CACHED_RESOURCES = [
  '/',
  '/offline',
  '/css/profile-menu.css',
  '/css/alerts.css',
  '/js/profile-menu.js',
  '/js/page-refresh-fix.js',
  '/js/event-initializer.js',
  '/images/favicon.png',
];

// Instalar el service worker
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Precargando recursos en caché: ' + CACHE_NAME);
        return cache.addAll(INITIAL_CACHED_RESOURCES);
      })
      .then(() => {
        // Fuerza que el service worker en espera se active inmediatamente
        return self.skipWaiting();
      })
  );
});

// Cuando se activa, limpiar cachés antiguas
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.filter(cacheName => {
          return cacheName.startsWith('sac-cache-') && cacheName !== CACHE_NAME;
        }).map(cacheName => {
          console.log('Eliminando caché antigua: ' + cacheName);
          return caches.delete(cacheName);
        })
      );
    }).then(() => {
      // Asegurar que el service worker controle todas las pestañas abiertas
      return self.clients.claim();
    })
  );
});

// Estrategia para solicitudes de navegación: network-first con fallback a caché
self.addEventListener('fetch', event => {
  // Solo manejar solicitudes GET
  if (event.request.method !== 'GET') return;
  
  // Obtener URL para comprobar tipo de recurso
  const url = new URL(event.request.url);
  
  // Parámetro especial para evitar caché
  const bypassCache = url.searchParams.has('no-cache');
  
  // Estrategia para recursos estáticos (CSS, JS, imágenes)
  if (
    url.pathname.endsWith('.css') || 
    url.pathname.endsWith('.js') || 
    url.pathname.includes('/images/')
  ) {
    if (bypassCache) {
      // Si se solicita explícitamente sin caché
      event.respondWith(fetchAndCache(event.request));
    } else {
      // Estrategia stale-while-revalidate para recursos estáticos
      event.respondWith(
        caches.match(event.request).then(cachedResponse => {
          const fetchPromise = fetchAndCache(event.request);
          return cachedResponse || fetchPromise;
        })
      );
    }
    return;
  }
  
  // Para solicitudes de navegación HTML, usar network-first
  if (event.request.headers.get('accept')?.includes('text/html')) {
    event.respondWith(
      fetch(event.request)
        .then(response => {
          // Crear una copia para la caché
          const responseClone = response.clone();
          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, responseClone);
          });
          return response;
        })
        .catch(() => {
          // Si la red falla, intentar desde la caché
          return caches.match(event.request)
            .then(cachedResponse => {
              if (cachedResponse) {
                return cachedResponse;
              }
              // Si no hay respuesta en caché, mostrar página offline
              return caches.match('/offline');
            });
        })
    );
    return;
  }
  
  // Estrategia por defecto: network-first
  event.respondWith(
    fetch(event.request)
      .then(response => {
        // Solo cachear respuestas exitosas
        if (!response || response.status !== 200 || response.type !== 'basic') {
          return response;
        }
        const responseClone = response.clone();
        caches.open(CACHE_NAME).then(cache => {
          cache.put(event.request, responseClone);
        });
        return response;
      })
      .catch(() => {
        return caches.match(event.request);
      })
  );
});

// Función para obtener recursos y guardarlos en caché
function fetchAndCache(request) {
  return fetch(request).then(response => {
    // Verificar si la respuesta es válida
    if (!response || response.status !== 200 || response.type !== 'basic') {
      return response;
    }
    
    const responseClone = response.clone();
    caches.open(CACHE_NAME).then(cache => {
      cache.put(request, responseClone);
    });
    
    return response;
  });
}

// Evento de mensaje para comunicación con la página
self.addEventListener('message', event => {
  if (event.data === 'skipWaiting') {
    self.skipWaiting();
  }
  
  if (event.data === 'clearCache') {
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          return caches.delete(cacheName);
        })
      );
    }).then(() => {
      event.ports[0].postMessage({ cleared: true });
    });
  }
});