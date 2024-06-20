let domain = location.origin,
	files = [],
	vwbBox, vwbVideo, vwbAudio, vwbImg,
	dir = location.pathname.replace(/^\//, '')
dir = dir ? dir.split('/') : []

const videoX = ['mp4', 'mkv', 'mov', 'avi'],
	audioX = ['mp3', 'aif', 'wav', 'ogg', 'wma', '3gp'],
	imageX = ['jpg', 'png', 'jpeg', 'gif', 'webp'],
	zipX = ['zip', '7z', 'gz', 'tar', 'rar'],
	// QuerySelector e: element (string: '.class') | a: all elements (boolean: true)
	__ = (e, a = false) => document[`querySelector${a ? "All" : ""}`](e) || null,
	// AddEventListener a: action (function) | e: element (string|HTML Node) | v: event type (strng: 'click')
	__e = (a, e = 'document', v = "click") => {
		let c = e != null && 'object' == typeof e ? e :
			(e == 'document' || !e || e == "" || e == null ? document : __(e, true))
		if (c == null || c.length == 0) return false
		return (!c[0] ? [c] : c).forEach(x => x.addEventListener(v, a))
	}

window.onload = () => {
	vwbBox = __('.vwb-box')
	vwbTitle = __('.vwb-title h3')
	vwbVideo = __('.vwb-box video')
	vwbAudio = __('.vwb-box audio')
	vwbImg = __('.vwb-box img')

	__e(() => closeBox(), '.vwb-box span')

	// Footer Menu
	__e(() => goHome(), '#mnu-home') // Footer menu home
	__e(() => alert('TODO: Upload'), '#mnu-upload')
	__e(() => alert('TODO: Excluir'), '#mnu-delete')
	__e(() => goBack(), '#mnu-back') // Footer menu back

	getScan()
}

const getDir = (path = '') => domain + '/scan/' + encodeURIComponent(dir.join('/') + '/' + path)
const getFile = (path = '') => domain + '/stream/' + encodeURIComponent(dir.join('/') + '/' + path)
const getDownload = (path = '') => domain + '/file/' + encodeURIComponent(dir.join('/') + '/' + path)

const clickfolder = path => {
	dir.push(path)
	getScan()
}

const clickfile = async (path, type) => {
	vwbTitle.innerText = path
	if (videoX.includes(type)) return playvideo(path)
	if (audioX.includes(type)) return playaudio(path)
	if (imageX.includes(type)) return imageview(path)

	// Otherwise download the file...
	location.href = getDownload(path)
}

const clickBreadcumbs = e => {
	if (dir.length == 0) return

	let i = e.currentTarget.dataset.path

	if (i == '{home}') return goHome()
	if (i == '{back}') return goBack()

	i = parseInt(i) + 1
	if (!dir[i]) return false

	dir = dir.slice(0, i)
	getScan()
}

const mountBreadcumbs = () => {
	const max = 70
	let out = ''
	if (dir.length > 0) out += '<li data-path="{back}"><span class="material-symbols-outlined">arrow_back</span></li>'
	out += '<li data-path="{home}"><span class="material-symbols-outlined">home</span></li>'

	const start = bcumb(max)
	if (start > 0) out += '<li data-path="{+}"><span class="material-symbols-outlined">add</span></li>'

	console.log('Start: ' + start)
	dir.map((d, i) => {
		if (i >= start) out += `<li data-path="${i}">${d}</li><span class="material-symbols-outlined">keyboard_arrow_right</span>`
	})
	__('#breadcumbs').innerHTML = out
	__e(e => clickBreadcumbs(e), '#breadcumbs li')
}

const bcumb = (max) => {
	const l = dir.length
	for (let i = l; i >= 0; i--) {
		let d = dir.slice(l - i)
		if (d.join(' / ').length <= max) return (l - i)
	}
	return 0
}


const getScan = async () => {
	let data = false
	try {
		f = await fetch(getDir())
		data = await f.json()
		data = data && data.data ? data.data : []
		files = data && data.dir && data.file ? data : []
		mountFiles()
		mountBreadcumbs()
	} catch {
		alert('Erro')
	}
}

const mountFiles = () => {
	if (files && files.dir && files.file) {
		let out = ''
		files.dir.map((d, i) => {
			out += `<li id="dir-${i}" class="file-folder" onclick="clickfolder('${d}')"><span class="material-symbols-outlined">folder</span><div class="filetitle"><span>${d}</span></div></li>`
		})

		files.file.map((f, i) => {
			let icon = 'cloud_download'
			if (videoX.includes(f.ext)) icon = 'play_circle'
			if (audioX.includes(f.ext)) icon = 'volume_up'
			if (imageX.includes(f.ext)) icon = 'image'
			if (zipX.includes(f.ext)) icon = 'folder_zip'

			out += `<li id="file-${i}" onclick="clickfile('${f.name}', '${f.ext}')"><span class="material-symbols-outlined">${icon}</span><div class="filetitle"><span>${f.name}</span><span class="fileinfo">${f.size}</span></div></li>`
		})
		__("#files").innerHTML = out
	}
}

const goBack = () => {
	dir.pop()
	getScan()
}
const goHome = () => {
	dir = []
	getScan()
}

// BOX VIEW ...
const closeBox = () => {
	vwbBox.classList.remove('on', 'video', 'audio', 'img')
	vwbVideo.pause()
	vwbVideo.src = ''
	vwbAudio.pause()
	vwbAudio.src = ''
	vwbImg.src = ''
}

const openBox = (t = 'img') => {
	vwbBox.classList.add('on', t)
}

const playvideo = (path) => {
	vwbTitle.innerText = path
	vwbVideo.src = getFile(path)
	vwbVideo.play()
	openBox('video')
}

const playaudio = (path) => {
	vwbTitle.innerText = path
	vwbAudio.src = getFile(path)
	vwbAudio.play()
	openBox('audio')
}

const imageview = (path) => {
	vwbTitle.innerText = path
	vwbImg.src = getFile(path)
	openBox('img')
}