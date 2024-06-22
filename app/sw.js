const CACHE='cache-1697121067404-dev';
const ASSETS=[
	"/",
	"/asset/favicon.ico",
	"/asset/icon/android-chrome-192x192.png",
	"/asset/icon/android-chrome-512x512.png",
	"/asset/icon/apple-touch-icon.png",
	"/asset/icon/browserconfig.xml",
	"/asset/icon/favicon-16x16.png",
	"/asset/icon/favicon-32x32.png",
	"/asset/icon/favicon.ico",
	"/asset/icon/mstile-150x150.png",
	"/asset/icon/safari-pinned-tab.svg",
	"/asset/icon/site.webmanifest",

	"/asset/img/l.gif",
	"/asset/img/og.jpg",

	"/asset/js/app.js",
	"/asset/js/browser.js",
	"/asset/js/directory.js",
	"/asset/js/utils.js",
	"/asset/js/viewbox.js",

	"asset/css/breadcrumbs.css",
	"asset/css/dirview.css",
	"asset/css/font.css",
	"asset/css/layout.css",
	"asset/css/root.css",
	"asset/css/style.css",
	"asset/css/utils.css",
	"asset/css/vbox.css",

	"asset/font/mso.woff2",
	"asset/font/os.woff2",
	"asset/font/vg.woff2"
];

/*
	Astro-sw
	File: D:\Projetos\Astro\Integration\Astro-sw\demo\src\sw\cache.js
*/
self.addEventListener('install', e =>
	e.waitUntil(
		caches.open(CACHE).then((cache) => {
			console.log(`[SWORKER install] caching "${CACHE}"`)
			cache.addAll(ASSETS)
		}).then(() => {
			sendMessage({ text: 'install' })
			self.skipWaiting()
		})
	)
)

self.addEventListener('activate', e =>
	e.waitUntil(
		caches.keys().then(async (ks) => {
			for (const k of ks) {
				if (k !== CACHE) {
					console.log(`[SWORKER removing] cache "${k}"`)
					await caches.delete(k)
				}
			}
			sendMessage({ text: 'activate', action: 'reset' })
			self.clients.claim()
		})
	)
)

/*
	Astro-sw
	File: D:\Projetos\Astro\Integration\Astro-sw\demo\src\sw\fetch.js
*/
const CONFIG = '/config'
self.addEventListener('fetch', (event) => {
	const {
		request,
		request: { url, method }
	} = event

	// Save||load json data in Cache Storage CONFIG
	if (url.match(CONFIG)) {
		if (method === 'POST') {
			request
				.json()
				.then((body) =>
					caches
						.open(CACHE)
						.then((cache) =>
							cache.put(
								CONFIG,
								new Response(JSON.stringify(body)),
							)
						)
				)
			return event.respondWith(new Response('{}'))
		} else {
			return event.respondWith(
				caches
					.match(CONFIG)
					.then((response) => response || new Response('{}'))
			)
		}
	} else {
		// Get & save request in Cache Storage
		if (method !== 'POST') {
			event.respondWith(
				caches.open(CACHE).then(async (cache) => {

					
					let response = await cache.match(event.request)
					if (response) return response

					// To fix 'chrome-extension'
					if (url.startsWith('chrome-extension') ||
						url.includes('extension') ||
						!(url.indexOf('http') === 0))
						return await fetch(event.request)

					response = await cache.match(event.request.url += 'index.html')
					if (response) return response

					response = await cache.match(event.request.url += '/index.html')
					if (response) return response

					response = await fetch(event.request)

					// To save the request in the cache.
					// 👇 It can cause problems if not carefully filtered.
					// if (ASSETS.length > 0) cache.put(event.request, response.clone())

					// if (ASSETS.length > 0 && (
					// 	url.startsWith('https://cdn.pixabay.com') ||
					// 	url.startsWith('https://fonts.g'))) cache.put(event.request, response.clone())

					return response
				})
			)
		}
	}
})

/*
	Astro-sw
	File: D:\Projetos\Astro\Integration\Astro-sw\demo\src\sw\message.js
*/
// Receive messages from the main script.
self.onmessage = function (e) {
	if (e.data.action === 'skipWaiting') {
		self.skipWaiting()
	}
	if (e.data.action === 'update') {
		self.registration.update()
	}
	if (e.data.action === 'sync') {
		self.registration.sync.register('sync-news')
			.then(() => {
				// ...
			})
	}
	sendMessage({ type: 'receive', msg: e.data })
}

// Send a message to the main script.
function sendMessage(message) {
	self.clients.matchAll().then(a => {
		if (a[0]) {
			self.clients.get(a[0].id).then(client => {
				client.postMessage(message)
			})
		}
	})
}


/*
	Astro-sw
	File: D:\Projetos\Astro\Integration\Astro-sw\demo\src\sw\notification.js
*/
self.addEventListener('notificationclick', (event) => {
	event.waitUntil(
		self.clients.matchAll().then((clientList) => {
			// console.log(
			// 	'[SWORKER notification] Notification click Received.',
			// 	clientList,
			// 	event.notification.data,
			// )

			var data =
				'undefined' !== typeof event.notification['data']
					? event.notification.data
					: {}

			event.notification.close()

			if (clientList.length > 0) {
				clientList[0].focus()
				return clientList[0].postMessage({
					msg: data,
					type: 'clientList[0]',
				})
			} else {
				self.clients
					.openWindow('/profile')
					.then((c) => {
						// console.log('[SWORKER client] OpenWindow: ', c)
						return c
					})
					.then((a) => {
						return a.postMessage({
							msg: data,
							type: 'clientList - clients - c',
						})
					})
			}
		}),
	)
})

/*
	Astro-sw
	File: D:\Projetos\Astro\Integration\Astro-sw\demo\src\sw\push.js
*/
self.addEventListener('push', (event) => {
	event.waitUntil(
		self.clients.matchAll().then((clientList) => {
			console.log(`[SWORKER push] Push had this data: "${event.data.text()}"`)

			const data = event.data.json()

			const title = data.title ?? 'Error'
			const options = {
				body: data.body ?? 'New message from the server!',
				icon: data.icon ?? '/icon/android-chrome-192x192.png',
				badge: data.badge ?? '/icon/favicon-32x32.png',
				image: data.image ?? '/asset/img/og.jpg',
				vibrate: data.vibrate ?? [],
				data: data.data ?? {}
			}

			// Focus ...
			const focused = clientList.some((client) => {
				client.postMessage({ msg: data, type: 'push' })
				return client.focused
			})

			if (focused) {
				// create an action for "focused" on the next line
				options.body += " [focused]"
			} else if (clientList.length > 0) {
				// create an action for "not focused" on the next line
				options.body += " [no focused]"
			} else {
				// create an action for "closed" on the next line
				options.body += " [closed]"
			}

			return self.registration.showNotification(title, options)
		}),
	)
})
