import { __, __e, __c, __glass } from "/asset/js/utils.js"

export default class BMenu {

	eBMenu = null
	eContainer = null

	eHome = null
	eBack = null
	eUpload = null
	eDelete = null

	constructor(){
		this.eContainer = __('#bmu-container')
		this.eBMenu = __c('ul', { id: 'bmenu', class: 'bmenu' })

		this.eHome = __c('li', {}, 'Home')
		this.eHome.append(__c('span', { class: 'material-symbols-outlined' }, 'home'))		
		this.eHome.onclick = () => this.goHome()

		this.eBack = __c('li', {}, 'Back')
		this.eBack.append(__c('span', { class: 'material-symbols-outlined' }, 'arrow_back'))
		this.eBack.onclick = () => this.goBack()

		this.eUpload = __c('li', {}, 'Upload')
		this.eUpload.append(__c('span', { class: 'material-symbols-outlined' }, 'publish'))
		this.eUpload.onclick = () => this.goUpload()

		this.eDelete = __c('li', {}, 'Delete')
		this.eDelete.append(__c('span', { class: 'material-symbols-outlined' }, 'delete'))
		this.eDelete.onclick = () => this.goDelete()
	}

	mount() {
		this.eBMenu.append(this.eHome)
		this.eBMenu.append(this.eBack)
		this.eBMenu.append(this.eUpload)
		this.eBMenu.append(this.eDelete)
		this.eContainer.append(this.eBMenu)
	}

	goHome () {
		window.location.href = '/'
	}

	goBack () {
		window.history.back()
	}

	goUpload () {
		alert('<< TODO >>\n\n1 - Upload\n2 - Download files\n3 - Ícone para download de video/audio/image')
	}

	goDelete () {
		if (confirm('Are you sure you want to delete this file?')) {
			alert('TODO: Delete')
		}
	}
}