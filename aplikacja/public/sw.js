// Prosty service worker: działanie offline (strona: najpierw sieć, zasoby: najpierw pamięć).
const CACHE = 'akademia-v5'
self.addEventListener('install', (e) => {
  e.waitUntil(caches.open(CACHE).then((c) => c.addAll(['./', './index.html', './manifest.webmanifest', './icon.svg'])))
  self.skipWaiting()
})
self.addEventListener('activate', (e) => {
  e.waitUntil(caches.keys().then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)))))
  self.clients.claim()
})
self.addEventListener('fetch', (e) => {
  const req = e.request
  if (req.method !== 'GET') return
  if (req.mode === 'navigate') {
    e.respondWith(fetch(req).then((r) => { caches.open(CACHE).then((c) => c.put('./index.html', r.clone())); return r })
      .catch(() => caches.match('./index.html')))
    return
  }
  e.respondWith(caches.match(req).then((hit) => hit || fetch(req).then((r) => {
    if (r.ok || r.type === 'opaque') { const copy = r.clone(); caches.open(CACHE).then((c) => c.put(req, copy)) }
    return r
  })))
})

// Przypomnienia od Wafla (Web Push). Treść przychodzi zaszyfrowana z guliwera4.pl.
self.addEventListener('push', (e) => {
  let d = {}
  try { d = e.data ? e.data.json() : {} } catch { d = { body: e.data ? e.data.text() : '' } }
  e.waitUntil(self.registration.showNotification(d.title || 'Akademia Guliwera', {
    body: d.body || 'Wafel czeka na Ciebie. 5 minut nauki?',
    icon: d.icon || './icon-192.png',
    badge: d.badge,
    tag: d.tag || 'akademia',
    renotify: false,
    lang: 'pl',
    data: { url: d.url || './' },
  }))
})
self.addEventListener('notificationclick', (e) => {
  e.notification.close()
  const url = (e.notification.data && e.notification.data.url) || './'
  e.waitUntil(self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((list) => {
    for (const c of list) { if (c.url.includes('/akademia') && 'focus' in c) { c.navigate(url).catch(() => {}); return c.focus() } }
    return self.clients.openWindow(url)
  }))
})
