import { __, __e, __c } from "/asset/js/utils.js"


export default class Directory {

	eDir = null
	eContainer = null

	Browser = null

	icon = {
		folder: 'folder',
		default: 'cloud_download',
		video: 'play_circle',
		audio: 'volume_up',
		image: 'image',
		zip: 'folder_zip'
	}
	
	mime = {
		video: ['mp4', 'mkv', 'mov', 'avi'],
		audio: ['mp3', 'aif', 'wav', 'ogg', 'wma', '3gp'],
		image: ['jpg', 'png', 'jpeg', 'gif', 'webp'],
		zip: ['zip', '7z', 'gz', 'tar', 'rar']
	}

	data = []

	constructor (Browser) {
		this.Browser = Browser
		this.eContainer = __('#dir-container')
		this.eDir = __c('div', { class: 'dir-view' })		
	}

	mount () {
		this.eContainer.innerHTML = ''
		this.eDir.innerHTML = ''
		this.eContainer.append(this.eDir)
	}

	setData(data) {
		this.data = data
	} 

	build() {
		if (this.data.dir.length == 0 && 
			this.data.file.length == 0) return this.eDir.innerHTML = '<li>Empty folder...</li>'
		if(this.data.dir) this.data.dir.map((d, i) => this.dirNode(i, d))
		if(this.data.file) this.data.file.map((f, i) => this.fileNode(i, f.name, f.size, f.ext))
	}

	// Search icon from file extension (mimeType?!)
	iconByExt (ext) {
		for(let x in this.mime) {
			if(this.mime[x].includes(ext)) return this.icon[x]
		}
		return this.icon.default
	}

	// Returns a list item type folder
	dirNode (id, name) {
		const li = __c('li', { class: 'dir-folder', id: 'dir-folder-' + id })
		li.append(
			__c('span', { class: 'material-symbols-outlined' }, this.icon.folder),
			__c('div', { class: 'dir-title' }, `<span>${name}</span>`))
		li.onclick = () => this.Browser.goDir(id)
		this.eDir.append(li)
	}

	fileNode (id, name, size, ext) {
		const li = __c('li', { id: 'dir-file-' + id })
		const icon = __c('span', { class: 'material-symbols-outlined' }, this.iconByExt(ext))
		const title = __c('div', { class: 'dir-title' }, `<span>${name}</span>`)
		title.append(__c('span', { class: 'dir-info' }, size))
		li.append(icon, title)

		li.onclick = () => this.Browser.goFile({
			id: id,
			name: name,
			ext: ext
		})
		this.eDir.append(li)
	}
}
