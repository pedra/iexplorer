import ViewBoxClass from "/asset/js/viewbox.js"
import BMenuClass from "/asset/js/bmenu.js"
import browser from "/asset/js/browser.js"


class App {

	VBox = null
	BMenu = null
	Dir = null
	Browser = null

	constructor() {
		this.Browser = browser
		this.VBox = new ViewBoxClass()
		this.BMenu = new BMenuClass()
	}

	async start() {
		this.BMenu.mount()
		this.VBox.mount()		
		this.Browser.mount()		
	}
}

const app = new App()

window.onload = () => {
	app.start()
	window.App = app
}
