import { __, __e, __c, __glass } from "/asset/js/utils.js"
import Directory from "/asset/js/directory.js"
import ViewBox from "/asset/js/viewbox.js"

export default class BrowserClass {

	eContainer = null
	eTitle = null
	eMenu = null
	eBreadcrumb = null

	bMax = 70

	domain = ''
	path = []
	data = []

	fileType = {
		video: ['mp4', 'mkv', 'mov', 'avi'],
		audio: ['mp3', 'aif', 'wav', 'ogg', 'wma', '3gp'],
		image: ['jpg', 'png', 'jpeg', 'gif', 'webp']
	}

	Directory = null

	constructor() {
		__glass()
		this.domain = location.origin

		this.eContainer = __('#hdr-container')
		this.eTitle = __c('h1', {})
		this.eMenu = __c('span', { class: 'top-menu material-symbols-outlined', id: 'top-menu' }, 'menu')
		this.eBreadcrumb = __c('ul', { id: 'breadcrumbs', class: 'breadcrumbs' })

		this.eTitle.onclick = () => this.goHome()
		this.eMenu.onclick = () => this.showMenu()
	}

	mount() {
		this.eContainer.innerHTML = ''
		this.eTitle.innerHTML = App.title

		const top = __c('div', { class: 'hdr-top', id: 'hdr-top' })
		top.append(this.eTitle, this.eMenu)

		this.eContainer.append(top, this.eBreadcrumb)
		this.scan()

		setTimeout(() => {
			this.eContainer.classList.add('on')
		}, 200)
	}

	getDir(path = '') {
		return this.domain + '/scan/' + encodeURIComponent(this.path.join('/') + '/' + path)
	}
	getFile(path = '') {
		return this.domain + '/stream/' + encodeURIComponent(this.path.join('/') + '/' + path)
	}
	getDownload(path = '') {
		return this.domain + '/file/' + encodeURIComponent(this.path.join('/') + '/' + path)
	}
	
	async scan() {
		try {
			__glass()
			const f = await fetch(this.getDir())
			const d = await f.json()

			if (d &&
				d.error === false &&
				d.data) {
				this.data = { dir: d.data.dir ?? [], file: d.data.file ?? [] }
				this.path = d.data.path ? d.data.path.replace(/^\/|\/$/g, '') : ''
				this.path = this.path == '' ? [] : this.path.split('/')

				// New Directory 
				this.Directory = new Directory(this)
				this.Directory.mount()
				this.Directory.setData(this.data)
				this.Directory.build()

				this.build()
				__glass(false)
				return this.data
			} else {
				alert('I couldn\'t load the directory!! 😕')
				return false
			}
		} catch (e) {
			alert('I couldn\'t load the directory!! 😯')
			return false
		}
	}

	build() {
		this.eBreadcrumb.innerHTML = ''
		
		if (this.path.length > 0) {
			const back = __c('li', {'data-path': '{back}'}, 
				__c('span', { class: 'material-symbols-outlined' }, 'arrow_back'))
			back.onclick = () => this.goBack()
			this.eBreadcrumb.append(back)
		}
		
		// let home = __c('li', {'data-path': '{home}'},
		// 		__c('span', { class: 'material-symbols-outlined' }, 'home'))
		// home.onclick = () => this.goHome()
		// this.eBreadcrumb.append(home)

		const start = this.#calcLen()
		if (start > 0) {			
			let plus = __c('li', {'data-path': '{+}'},
				__c('span', { class: 'material-symbols-outlined' }, 'more_horiz'))
			plus.onclick = () => this.goPlus()
			this.eBreadcrumb.append(plus)
		}

		this.path.map((d, i) => {
			if (i >= start) {
				let s = __c('li', { 'data-path': i }, d)
				s.onclick = () => this.goTo(i)
				this.eBreadcrumb.append(s, __c('span', { class: 'material-symbols-outlined' }, 'keyboard_arrow_right'))
			}
		})

		this.eContainer.append(this.eBreadcrumb)
	}

	#calcLen () {
		const l = this.path.length
		for (let i = l; i >= 0; i--) {
			if (this.path.slice(l - i).join(' / ').length <= this.bMax) return (l - i)
		}
		return 0
	}

	showMenu() {
		alert('TODO: Show Menu!')
	}

	goBack() {
		this.path.pop()
		this.scan()
		// TODO: add navigation like -> window.history.back()
	}

	goHome() {
		this.path = []
		this.scan()
		// TODO: add navigation like -> window.location.href = '/'
	}

	goPlus() {
		console.log('goPlus')
	}	

	goTo(i) {
		i += 1
		if (!this.path[i]) return false
		this.path = this.path.slice(0, i)
		this.scan()
	}

	goFile({
		id: id,
		name: name,
		ext: ext
	}){
		const Stream = new ViewBox()
		Stream.mount()
		
		for(let t in this.fileType){
			if(this.fileType[t].includes(ext)){
				Stream.show(name, t, this.getFile(name))
				return true
			}
		}
		// Otherwise download the file...
		this.download(name)
	}

	goDir(id) {
		this.path.push(this.data.dir[id])
		this.scan()
	}

	download(name) {
		location.href = this.getDownload(name)
	}

}