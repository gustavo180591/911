

/**************************************************** WORKBOX IMPORT ****************************************************/
// The configuration is set to use Workbox
// The following code will import Workbox from CDN or public URL
// Import from public URL

importScripts('/workbox/workbox-sw.js');
workbox.setConfig({modulePathPrefix: '/workbox'});
/**************************************************** END WORKBOX IMPORT ****************************************************/






/**************************************************** CACHE STRATEGY ****************************************************/
// Strategy: CacheFirst
// Match: ({url}) => url.pathname.startsWith('/assets')
// Cache Name: assets
// Enabled: 1
// Needs Workbox: 1
// Method: 

// 1. Creation of the Workbox Cache Strategy object
// 2. Register the route with the Workbox Router
// 3. Add the assets to the cache when the service worker is installed
const cache_0_0 = new workbox.strategies.CacheFirst({
  cacheName: 'assets',plugins: [new workbox.expiration.ExpirationPlugin({
    "maxEntries": 38,
    "maxAgeSeconds": 31536000
})]
});
workbox.routing.registerRoute(({url}) => url.pathname.startsWith('/assets'),cache_0_0);
self.addEventListener('install', event => {
  const done = [
    "/assets/@spomky-labs/pwa-bundle/backgroundsync-form_controller-ynS5Onz.js",
    "/assets/@spomky-labs/pwa-bundle/sync-broadcast_controller-2-cTsg3.js",
    "/assets/@spomky-labs/pwa-bundle/connection-status_controller-6uY2aFO.js",
    "/assets/@spomky-labs/pwa-bundle/prefetch-on-demand_controller-e2Rt_GG.js",
    "/assets/@symfony/ux-map/abstract_map_controller-VQh9I3a.js",
    "/assets/@symfony/ux-turbo/turbo_controller-zl4y2v3.js",
    "/assets/@symfony/ux-turbo/turbo_stream_controller-UOrik8H.js",
    "/assets/@symfony/stimulus-bundle/loader-hwmZoC6.js",
    "/assets/@symfony/stimulus-bundle/controllers-rSGaVI7.js",
    "/assets/sw-qz2Fc7I.js",
    "/assets/vendor/@hotwired/stimulus/stimulus.index-tbHQDkJ.js",
    "/assets/vendor/@hotwired/turbo/turbo.index-gQ9E7xo.js",
    "/assets/bootstrap-xCO4u8H.js",
    "/assets/icon-192x192-4Lo7b5N.png",
    "/assets/app-jgPm2-L.js",
    "/assets/styles/app-va-52cO.css",
    "/assets/controllers/hello_controller-VYgvytJ.js",
    "/assets/controllers/csrf_protection_controller-G1G8BJh.js",
    "/assets/icon-512x512-NLxhnMS.png"
].map(
    path =>
      cache_0_0.handleAll({
        event,
        request: new Request(path),
      })[1]
  );
  event.waitUntil(Promise.all(done));
});

/**************************************************** END CACHE STRATEGY ****************************************************/





/**************************************************** CACHE STRATEGY ****************************************************/
// Strategy: CacheFirst
// Match: ({request}) => request.destination === 'font'
// Cache Name: fonts
// Enabled: 1
// Needs Workbox: 1
// Method: GET

// 1. Creation of the Workbox Cache Strategy object
// 2. Register the route with the Workbox Router
// 3. Add the assets to the cache when the service worker is installed
const cache_2_0 = new workbox.strategies.CacheFirst({
  cacheName: 'fonts',plugins: [new workbox.cacheableResponse.CacheableResponsePlugin({
    "statuses": [
        0,
        200
    ]
}), new workbox.expiration.ExpirationPlugin({
    "maxEntries": 60,
    "maxAgeSeconds": 31536000
})]
});
workbox.routing.registerRoute(({request}) => request.destination === 'font',cache_2_0,'GET');
/**************************************************** END CACHE STRATEGY ****************************************************/





/**************************************************** CACHE STRATEGY ****************************************************/
// Strategy: StaleWhileRevalidate
// Match: ({url}) => url.origin === 'https://fonts.googleapis.com'
// Cache Name: google-fonts-stylesheets
// Enabled: 1
// Needs Workbox: 1
// Method: 

// 1. Creation of the Workbox Cache Strategy object
// 2. Register the route with the Workbox Router
// 3. Add the assets to the cache when the service worker is installed
const cache_3_0 = new workbox.strategies.StaleWhileRevalidate({
  cacheName: 'google-fonts-stylesheets',plugins: []
});
workbox.routing.registerRoute(({url}) => url.origin === 'https://fonts.googleapis.com',cache_3_0);
/**************************************************** END CACHE STRATEGY ****************************************************/





/**************************************************** CACHE STRATEGY ****************************************************/
// Strategy: CacheFirst
// Match: ({url}) => url.origin === 'https://fonts.gstatic.com'
// Cache Name: google-fonts-webfonts
// Enabled: 1
// Needs Workbox: 1
// Method: 

// 1. Creation of the Workbox Cache Strategy object
// 2. Register the route with the Workbox Router
// 3. Add the assets to the cache when the service worker is installed
const cache_3_1 = new workbox.strategies.CacheFirst({
  cacheName: 'google-fonts-webfonts',plugins: [new workbox.cacheableResponse.CacheableResponsePlugin({
    "statuses": [
        0,
        200
    ]
}), new workbox.expiration.ExpirationPlugin({
    "maxEntries": 30,
    "maxAgeSeconds": 31536000
})]
});
workbox.routing.registerRoute(({url}) => url.origin === 'https://fonts.gstatic.com',cache_3_1);
/**************************************************** END CACHE STRATEGY ****************************************************/





/**************************************************** CACHE STRATEGY ****************************************************/
// Strategy: CacheFirst
// Match: ({request, url}) => (request.destination === 'image' && !url.pathname.startsWith('/assets'))
// Cache Name: images
// Enabled: 1
// Needs Workbox: 1
// Method: 

// 1. Creation of the Workbox Cache Strategy object
// 2. Register the route with the Workbox Router
// 3. Add the assets to the cache when the service worker is installed
const cache_4_0 = new workbox.strategies.CacheFirst({
  cacheName: 'images',plugins: []
});
workbox.routing.registerRoute(({request, url}) => (request.destination === 'image' && !url.pathname.startsWith('/assets')),cache_4_0);
/**************************************************** END CACHE STRATEGY ****************************************************/





/**************************************************** CACHE STRATEGY ****************************************************/
// Strategy: StaleWhileRevalidate
// Match: ({url}) => '/site.webmanifest' === url.pathname
// Cache Name: manifest
// Enabled: 1
// Needs Workbox: 1
// Method: 

// 1. Creation of the Workbox Cache Strategy object
// 2. Register the route with the Workbox Router
// 3. Add the assets to the cache when the service worker is installed
const cache_5_0 = new workbox.strategies.StaleWhileRevalidate({
  cacheName: 'manifest',plugins: []
});
workbox.routing.registerRoute(({url}) => '/site.webmanifest' === url.pathname,cache_5_0);
/**************************************************** END CACHE STRATEGY ****************************************************/




/**************************************************** CACHE CLEAR ****************************************************/
// The configuration is set to clear the cache on each install event
// The following code will remove all the caches
self.addEventListener("install", function (event) {
    event.waitUntil(caches.keys().then(function (cacheNames) {
            return Promise.all(
                cacheNames.map(function (cacheName) {
                    return caches.delete(cacheName);
                })
            );
        })
    );
});
/**************************************************** END CACHE CLEAR ****************************************************/



if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/service-worker.js')
    .then(() => console.log('Service Worker registrado con éxito'))
    .catch((error) => console.error('Error registrando el Service Worker:', error));
}