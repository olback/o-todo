/*
    o-todo - service-worker
    https://github.com/olback/o-todo

*/

'use strict';

// Incrementing CACHE_VERSION will kick off the install event and force previously cached
// resources to be cached again.
const CACHE_VERSION = 1;
let CURRENT_CACHES = {
  offline: 'offline-v' + CACHE_VERSION,
  assets: 'assets-v' + CACHE_VERSION
};
const OFFLINE_URL = 'assets/offline.html';

function createCacheBustedRequest(url) {
  let request = new Request(url, {cache: 'reload'});
  if ('cache' in request) {
    return request;
  }

  // If {cache: 'reload'} didn't have any effect, append a cache-busting URL parameter instead.
  let bustedUrl = new URL(url, self.location.href);
  bustedUrl.search += (bustedUrl.search ? '&' : '') + 'cachebust=' + Date.now();
  return new Request(bustedUrl);
}

self.addEventListener('install', event => {
  event.waitUntil(
    // We can't use cache.add() here, since we want OFFLINE_URL to be the cache key, but
    // the actual URL we end up requesting might include a cache-busting parameter.
    fetch(createCacheBustedRequest(OFFLINE_URL)).then(function(response) {
      return caches.open(CURRENT_CACHES.offline).then(function(cache) {
        return cache.put(OFFLINE_URL, response);
      });
    })
  )
  event.waitUntil(
    caches.open(CURRENT_CACHES.assets).then(function(cache) {
      return cache.addAll(
        [
          'assets/css/font-awesome.min.css',
          'assets/css/main.min.css',
          'assets/fonts/CabinSketch-Regular.ttf',
          'assets/fonts/fontawesome-webfont.ttf',
          'assets/fonts/Roboto-Regular.ttf',
          'assets/images/profile_img.png',
          'assets/images/sidenav-bg.png',
          'assets/main.js',
          'assets/icons/icon-96.png'
        ]
      );
    })
  )
});

self.addEventListener('activate', event => {
  let expectedCacheNames = Object.keys(CURRENT_CACHES).map(function(key) {
    return CURRENT_CACHES[key];
  });

  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (expectedCacheNames.indexOf(cacheName) === -1) {
            // If this cache name isn't present in the array of "expected" cache names,
            // then delete it.
            console.log('Deleting out of date cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

self.addEventListener('fetch', event => {
  if (event.request.mode === 'navigate' ||
      (event.request.method === 'GET' &&
       event.request.headers.get('accept').includes('text/html'))) {
    console.log('Handling fetch event for', event.request.url);
    event.respondWith(
      fetch(event.request).catch(error => {
        console.log('Fetch failed; returning offline page instead.', error);
        return caches.match(OFFLINE_URL);
      })
    );
  }
});

self.addEventListener('fetch', function(event) {
    if(!event.request.headers.get('accept').includes('text/html')) {
      event.respondWith(
        caches.match(event.request).then(function(response) {
          return response || fetch(event.request);
        })
      );
    }
});
