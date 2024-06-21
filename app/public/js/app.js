import ViewBox from "/asset/js/viewbox.js"
import BMenu from "/asset/js/bmenu.js"
// import Directory from "/asset/js/directory.js"
import Browser from "/asset/js/browser.js"


class App {

	VBox = null
	BMnu = null
	Dir = null
	Brow = null

	constructor() {
		this.Brow = Browser
		this.VBox = new ViewBox()
		this.BMnu = new BMenu(this.Brow)
	}

	async start() {
		this.BMnu.mount()
		this.VBox.mount()		
		this.Brow.mount()
		
	}
}

const app = new App()

window.onload = () => {
	app.start()
	window.App = app
}
