import ViewBoxClass from "/asset/js/viewbox.js"
// import BMenuClass from "/asset/js/bmenu.js"
import BrowserClass from "/asset/js/browser.js"


class App {

	VBox = null
	BMenu = null
	Dir = null
	Browser = null

	title = 'iExplorer'

	constructor() {
		this.Browser = new BrowserClass()
		this.VBox = new ViewBoxClass()
		// this.BMenu = new BMenuClass()
	}

	async start() {
		// this.BMenu.mount()
		this.VBox.mount()		
		this.Browser.mount()		
	}
}

const app = new App()

window.onload = () => {
	window.App = app
	app.start()

	// Service Worker install
	'serviceWorker' in navigator && navigator.serviceWorker.register('/sw.js')

	// Colar isso em Aplicativo + Service workers + Enviar por Push
	// {"title": "iExplorer", "body": "Hello world!!", "action": "reset"}

	// Notification.requestPermission(function (result) {
	// 	console.log("User choice", result);
	// 	if (result !== "granted") {
	// 		console.log("No notification permission granted!");
	// 	} else {
	// 		configurePushSub();// Write your custom function that pushes your message
	// 	}
	// });
}
