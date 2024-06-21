import { __, __e, __c } from "/asset/js/utils.js"

export default class ViewBox {

	eBox = null
	eContainer = null

	eTitle = null
	eView = null
	players = {
		video: null,
		audio: null,
		image: null
	}

	constructor() {
		this.eContainer = __('#vbox-container')
		this.eBox = __c('div', {class: 'vwb-box'})

		this.eTitle = __c('h3')		
		const close = __c('span', { class: 'material-symbols-outlined' }, 'close')
		
		close.onclick = () => this.hide()
		this.eBox.onclick = (e) => {
			if(e.target.classList.contains('vwb-box')) this.hide()
		}
		
		const boxTitle = __c('div', { class: 'vwb-title' })
		boxTitle.append(this.eTitle, close)

		this.eView = __c('div', { class: 'vwb-view' })
		this.eBox.append(boxTitle, this.eView)	

		this.players.video = __c('video', { controls: true, autoplay: true, class: 'vwb-video', id: 'vwb-video' })
		this.players.audio = __c('audio', { controls: true, autoplay: true, class: 'vwb-audio', id: 'vwb-audio' })
		this.players.image = __c('img', { class: 'vwb-image', id: 'vwb-image' })
	}

	mount () {
		this.eContainer.innerHTML = ''
		this.eContainer.append(this.eBox)
	}

	hide () {
		this.eBox.classList.remove('on', 'video', 'audio', 'img')
		this.players.video.pause()
		this.players.audio.pause()
		setTimeout(() => this.eView.innerHTML = '', 400)
	}

	/**
	 * @param {string} title 
	 * @param {string} type - video|audio|image
	 * @param {string} source - url|data64
	 */
	show (title, type, source) {
		this.eTitle.innerText = title
		this.eView.innerHTML = ''

		this.players[type].src = source
		this.eView.append(this.players[type])
		this.eBox.classList.add('on', type)
	}
}