// public/sw.js
// VERSION: bump this to invalidate old caches when you change files
const SW_VERSION = 'v3-2025-10-15';
const CACHE_NAME = `video-cache-${SW_VERSION}`;

// List the exact assets you want cached.
// Use your real, hashed URLs if available to improve cache hits.
const PRECACHE_URLS = [
  // Posters / small assets first (fast win)
  '/frontend/assets/images/fallback-poster.jpg',

  // Videos — keep this list reasonable in size!
  '/frontend/assets/images/gymm.mp4',      // banner
  '/frontend/assets/images/table-1.mp4',
  '/frontend/assets/images/table-2.mp4',
  '/frontend/assets/images/table-3.mp4',
];

// During install, prefetch core assets
self.addEventListener('install', (event) => {
  event.waitUntil((async () => {
    const cache = await caches.open(CACHE_NAME);
    // Use "cache.addAll" for simple prefetch. For large mp4s this can be heavy;
    // it will still stream into cache in background.
    await cache.addAll(PRECACHE_URLS);
    // Activate immediately on first load (optional)
    await self.skipWaiting();
  })());
});

// Clean up old caches on activate
self.addEventListener('activate', (event) => {
  event.waitUntil((async () => {
    const keys = await caches.keys();
    await Promise.all(
      keys
        .filter((k) => k.startsWith('video-cache-') && k !== CACHE_NAME)
        .map((k) => caches.delete(k))
    );
    await self.clients.claim();
  })());
});

// Cache-first strategy for listed videos/posters; network fallback for others
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Only handle GETs, same-origin
  if (request.method !== 'GET' || url.origin !== location.origin) return;

  // If request is one of our prelisted assets => cache-first
  const isPrecached = PRECACHE_URLS.some((p) => url.pathname === p || url.pathname.endsWith(p));
  if (isPrecached) {
    event.respondWith((async () => {
      const cache = await caches.open(CACHE_NAME);
      const cached = await cache.match(request, { ignoreVary: true });
      if (cached) return cached;

      try {
        const res = await fetch(request, { credentials: 'same-origin' });
        if (res && res.ok) cache.put(request, res.clone());
        return res;
      } catch (e) {
        // Optional: fallback to poster if video fails
        if (request.destination === 'video') {
          const poster = await cache.match('/frontend/assets/images/fallback-poster.jpg');
          if (poster) return poster;
        }
        throw e;
      }
    })());
  }
  // Else: let it pass through (or implement your general strategy)
});
