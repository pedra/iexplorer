<?php
define('SEP', DIRECTORY_SEPARATOR);
$gpath = isset($_GET['path']) && $_GET['path'] != "" ? $_GET['path'] : '';
$path = realpath(__DIR__ . $gpath);

if(!is_dir($path) || !str_starts_with($path, __DIR__)) {
    header('Location: /');
    exit;
}



/**
 * STREAM FILES ----------------------------------------------------------------
 */
//$tmp_dir = realpath(dirname(dirname(dirname(__DIR__)))); 
$tmp_dir = __DIR__;

if(isset($_GET['file']) && $_GET['file'] != ""){
	$file = realpath($tmp_dir . '/' . $_GET['file']);
	if(!is_dir($file) || !str_starts_with($file, $tmp_dir)) {
		include_once './stream.php';
		$stm = new Stream($file);
		$stm->start();
	}
	exit;
}



//file_put_contents(__DIR__ . '/server.log', print_r($_SERVER, true), FILE_APPEND);

/*
$files = "";
$diretorio = dir($path);

 
while($arquivo = $diretorio -> read()){
    if( $arquivo == '.' || 
        $arquivo == '..' || 
        $arquivo == 'server.log' || 
        $arquivo == 'index.php' || 
        $arquivo == '_start_.bat') continue;

    if(is_dir($path.SEP.$arquivo)) {
        $files .= '<li class="file-folder" onclick="clickfolder(\''.$arquivo.
                    '\')"><span class="material-symbols-outlined">folder</span>';
    } else {
        $files .= '<li onclick="clickfile(\''.$arquivo.
                    '\')"><span class="material-symbols-outlined">file_download</span>';
    }

    $files .= $arquivo.'</li>';
}

$diretorio -> close();
*/

// TESTE
function human_filesize($bytes, $decimals = 2) {
    $factor = floor((strlen($bytes) - 1) / 3);
    if ($factor > 0) $sz = 'KMGT';
    return sprintf("%.{$decimals}f&nbsp;", $bytes / pow(1024, $factor)) . @$sz[$factor - 1] . 'B';
}

$videoX = ['mp4', 'mkv', 'mov', 'avi'];
$audioX = ['mp3', 'aif', 'wav', 'ogg', 'wma', '3gp'];
$imageX = ['jpg', 'png', 'jpeg', 'gif', 'webp'];
$zipX = ['zip', '7z', 'gz', 'tar', 'rar'];
$sf = '';
$sd_dir = '';
$sd_file = '';
$sd = scandir($path);
foreach($sd as $d){
	if(	$d == '.'|| 
		$d == '' ||
		$d == 'server.log' ||
		$d == 'index.php' ||
		$d == '_start_.bat') continue;
		
	if(is_dir($path.'/'.$d)) {
		$sd_dir .= '<li class="file-folder" onclick="clickfolder(`'.$d.'`)"><span class="material-symbols-outlined">folder</span><div class="filetitle"><span>'.$d.'</span></div></li>';
	} else {
		$sd_icon = 'file_download';

		$sd_type = strtolower(pathinfo($path.'/'.$d)['extension'] ?? '');
		
		// Icons
		if(in_array($sd_type, $videoX)) $sd_icon = 'play_circle';
		if(in_array($sd_type, $audioX)) $sd_icon = 'volume_up';
		if(in_array($sd_type, $imageX)) $sd_icon = 'image';
		if(in_array($sd_type, $zipX)) $sd_icon = 'folder_zip';

		// if($sd_type == 'mp4' || $sd_type == 'mkv' || $sd_type == 'mov') $sd_icon = 'play_circle';
		
		// if($sd_type == 'jpg' || $sd_type == 'png' || $sd_type == 'jpeg' || $sd_type == 'gif') $sd_icon = 'image';
		
		// if($sd_type == 'mp3'|| $sd_type == 'aif'|| $sd_type == 'wav' || $sd_type == 'ogg' || $sd_type == 'wma' || $sd_type == '3gp') $sd_icon = 'volume_up';
		
		// if($sd_type == 'zip' || $sd_type == '7z' || $sd_type == 'gz' || $sd_type == 'tar' || $sd_type == 'rar') $sd_icon = 'folder_zip';

		$sd_size = human_filesize(filesize($path.'/'.$d));

		$sd_file .= '<li onclick="clickfile(`'.$d.'`, \''.$sd_type.'\')"><span class="material-symbols-outlined">'.$sd_icon.'</span><div class="filetitle"><span>'.$d.'</span><span class="fileinfo">'.$sd_size.'</span></div></li>';
	}
}

$files = $sd_dir.$sd_file;

// Breadcumbs
$bdcpt = str_replace(SEP, '/', ltrim(str_replace(__DIR__, "", $path), SEP));
$bdc = explode('/', $bdcpt);

$npt = $breadcumbs = "";
foreach($bdc as $bc){
    if($bc == "") break;
    $npt .= "/" . $bc;
    $breadcumbs .= '/<a href="' . ('/' . $bdcpt == $npt ? 
        '" onclick="return false">' : 
        '/?path=' . $npt . '">') . $bc . '</a>';
}
// Insert icons
$breadcumbs = $breadcumbs != '' ? 
    '<span class="material-symbols-outlined" id="bcb-back">arrow_back</span><span class="material-symbols-outlined" onclick="location.href=\'/\'">home</span>' . $breadcumbs :
    '<span class="material-symbols-outlined">home</span>/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Explorer</title>
    <link rel="shortcut icon" type="image/png" sizes="16x16" href="data:image/x-icon;charset=utf-8;base64,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wM/Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3RDSgAAAAAAAAAAAAAAFfH0c3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3NzxQYAAAAAAAAAAAApiHPzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nz13DAAAAAAAAAAAAoqzNzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3NzdFajgAAAAAAAAAtnM/Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc1RwgAAAAAAAI6F0c3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3PnIcAAAAAFVRnzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nz1OOAAAAOUbPzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3NzWcTnZQ+oc/Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3PZiWwz83Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc0AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA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"/>


    <style>
    /* Material Icons by Google */
	@font-face {
	font-family: 'Material Symbols Outlined';
	font-style: normal;
	font-weight: 100 700;
}

.material-symbols-outlined {
  font-family: 'Material Symbols Outlined';
  font-weight: normal;
  font-style: normal;
  font-size: 24px;
  line-height: 1;
  letter-spacing: normal;
  text-transform: none;
  display: inline-block;
  white-space: nowrap;
  word-wrap: normal;
  direction: ltr;
  -webkit-font-feature-settings: 'liga';
  -webkit-font-smoothing: antialiased;
  font-variation-settings:
  'FILL' 0,
  'wght' 400,
  'GRAD' 0,
  'opsz' 24
}

.material-symbols-outlined.not {
  font-variation-settings:
  'FILL' 1,
  'wght' 400,
  'GRAD' 0,
  'opsz' 24
}

/* THEME DEFAULT ------------------------------------------------------------------ */
:root {
    --primary: #b71c1c;
    --secondary: #0060c1;
    --tertiary: #ff8000;
    --hover: #889900;

    /* PAGE */
    --text: #222222;
    --bg: #f4f4f4;

    /* PAGE DARK */
    --text-dark: #f1f1f1;
    --bg-dark: #681010;

    /* WHITE SCALE */
    --white: #ffffff;
    --white-1: #f9f9f9;
    --white-2: #f4f4f4;
    --white-3: #f1f1f1;
    --white-4: #e8e8e8;
    --white-5: #dddddd;
    --white-6: #cccccc;
    --white-7: #bbbbbb;
    --white-8: #aaaaaa;
    --white-9: #999999;

    /* BLACK SCALE */
    --black: #000000;
    --black-1: #222222;
    --black-2: #261414;
    --black-3: #333333;
    --black-4: #444444;
    --black-5: #570505;
    --black-6: #666666;
    --black-7: #777777;
    --black-8: #888888;

    /* COMMON */
    --red: #b71c1c;
    --red-light: #c62828;
    --red-dark: #681010;
    --blue: #0060c1;
    --blue-light: #534caf;
    --blue-dark: #040086;
    --green: #889900;
    --yellow: #e6ff00;
    --orange: #ff8000;

    /* ELEVATION */
    --elevation-1: 0 0 2px rgba(0, 0, 0, 0.2);
    --elevation-2: 0 3px 6px rgba(0, 0, 0, 0.2);
    --elevation-3: 0 6px 12px rgba(0, 0, 0, 0.2);
    --elevation-4: 0 10px 20px rgba(0, 0, 0, 0.4);
    --elevation-5: 0 10px 30px rgba(0, 0, 0, 0.4);
    --elevation-6: 0 20px 50px rgba(0, 0, 0, 0.2);

    /* RADIUS */
    --radius: 4px;
    --radius-1: 8px;
    --radius-2: 1rem;
    --radius-3: 1.5rem;
    --radius-4: 2rem;
    --radius-5: 50%;
}

/* STYLE ------------------------------------------------------------------ */
*,
*:before,
*:after {
    outline: none;
    box-sizing: border-box;
    -moz-appearance: none;
    -webkit-appearance: none;
    appearance: none;
}

html,
body,
button,
input,
select,
label,
table,
textarea {
    font-size: 16px;
    font-family: BlinkMacSystemFont, -apple-system, "Segoe UI", system-ui, Roboto,
        "Helvetica Neue", Ubuntu, Tahoma, sans-serif, "Apple Color Emoji",
        "Segoe UI Emoji", "Segoe UI Symbol";
}

html,
body {
    height: 100%;
    overflow: hidden;
    background-size: cover;
    background: var(--white-4);
    color: var(--text);
    width: 100%;
    margin: 0;
    padding: 0;
}

body {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 100%;
    min-width: 100%;
}

hr {
    clear: both;
}

sup {
    margin: -0.2rem 0.1rem 0 0;
    font-size: 0.7rem;
}

h1 {
    font-size: 4rem;
    font-weight: 400;
    color: var(--text);
    text-shadow: none;
    /* text-align: center; */
}

p {
    color: var(--text);
    text-shadow: none;
    clear: both;
}

.container {
    min-width: 200px;
    max-width: 1024px;
    width: 100%;
    margin: 0 auto;
    padding: 0 1rem;
}


/** LAYOUT
----------------------------------------- */
header,
footer {
    min-height: 50px;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 6000;

    background: var(--black);
    color: var(--text-dark);
}

header {
    box-shadow: var(--elevation-6);
}

main {
    display: flex;
    flex: 1;
    overflow: auto;
}

header .container {
    display: flex;
    justify-content: initial;
    align-items: center;
}


/** BUTTON
----------------------------------------- */
button {
    transition: 0.4s;
    min-width: 5rem;
    min-height: 2.5rem;
    cursor: pointer;
    background: var(--secondary);
    border: none;
    border-radius: var(--radius);
    color: var(--white);
    text-shadow: none;
    box-shadow: var(--elevation-1);
}

button:hover {
    color: var(--white);
    background: var(--hover);
}

button .material-icons {
    font-size: 1.3rem;
    margin: 0.2rem 0.5rem 0 0;
}

button.btn {}

button.btn-secondary {
    color: var(--white);
    background: var(--tertiary);
}

button.btn-danger {
    color: var(--white);
    background: var(--primary);
}

/** INPUT
----------------------------------------- */

label {
    font-size: 0.8rem;
    margin: 0.5rem 0;
    padding: 0 0 0 0.5rem;
    display: block;
}

input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
select,
textarea {
    background: var(--white);
    border: none;
    border-radius: var(--radius-2);
    border-bottom: 1px solid var(--white-7, #cccccc);
    box-shadow: 1px 2px 4px rgba(0, 0, 0, 0.1) inset;
    padding: 0.5rem;
    margin: 0 0 1rem 0;
    width: 100%;
    min-height: 1rem;
}

input[type="checkbox"],
input[type="radio"] {
    width: 1rem;
    height: 1rem;
    float: left;
    margin: 0.2rem 0.5rem 0 0;
    -moz-appearance: auto;
    -webkit-appearance: auto;
    appearance: auto;
}

input[type="file"] {
    opacity: 0;
    position: absolute;
    height: 3rem;
    width: 3rem;
    margin: 0;
}

/** HEAD
----------------------------------------- */
header h1 {
    margin: 0;
    padding: .5rem 0;
    font-size: 1.8rem;
    color: inherit;
}

header span.material-symbols-outlined {
    font-size: 2.2rem !important;
    margin: 0 .5rem 0 0;
    color: var(--tertiary);
}

/** MAIN
----------------------------------------- */
main h3 {
    font-weight: 200;
    color: var(--text);
    display: flex;
}

main h3 span.material-symbols-outlined {
    /* font-size: 1.2rem;
    border: 1px solid var(--black);
    border-radius: var(--radius-5); */
    margin: 0 .5rem;
    cursor: pointer;
}

main h3 a {
    text-decoration: none;
    color: var(--text);
    font-weight: 700;
    display: block;
    padding: 0 .5rem;
}

main h3 a:hover {
    border-bottom: 1px solid var(--text);
}

ul.files {
    list-style: none;
    padding: 1rem 0;
}

ul.files li {
    padding: .5rem;
    /* margin: 0 0 .5rem 0; */
    display: flex;
    align-items: center;
    /* background: var(--white-3);
    box-shadow: var(--elevation-1);
    border-radius: var(--radius); */
    overflow-wrap: anywhere;
    cursor: pointer;
	border-bottom: 1px dashed #bbb;
}

ul.files li:hover,
ul.files li:active {
    background: var(--white);
}

ul.files li:hover a,
ul.files li:active a {
    color: var(--blue-dark);
}

ul.files li span.material-symbols-outlined {
    margin: 0 .5rem 0 0;
    color: var(--blue-dark);
}

ul.files li.file-folder i {
    color: var(--orange);
}

ul.files li a {
    text-decoration: none;
    color: var(--black);
    font-size: 1.2rem;
}

ul.files li .filetitle {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

ul.files li .fileinfo {
	font-size: .8rem;
	overflow-wrap: normal;
}

/** FOOTER
----------------------------------------- */
footer .bmenu {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: row;
    justify-content: space-around;
    align-items: center;
    cursor: pointer;
}

footer .bmenu li {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: var(--tertiary);
    padding: .5rem 0;
}

footer .bmenu li span.material-symbols-outlined {
    margin: .2rem 0;
}

footer .bmenu li span {
    font-size: .8rem;
    color: var(--white-7);
}

/** PLAYER
------------------------------------------ */
.videoplayer,
.audioplayer,
.imageview {
	display: none;
	z-index: -1;
	transform: scale(0);
	transition: .6s;
	justify-content: center;
	align-items: center;
	background: rgba(0, 0, 0, 0.7);
}

.videoplayer,
.imageview {	
	position: fixed;
	inset: 0;
}

.audioplayer {
	position: absolute;
    inset: auto 0 0 0;
}

.videoplayer video,
.audioplayer audio,
.imageview img {
	max-width: 100%;
	max-height: calc(100vh - 116px);
}

.videoplayer.active,
.audioplayer.active,
.imageview.active {
	display: flex;
	z-index: 10000;
	transform: none;
	flex-direction: row-reverse;
    gap: 1rem;
}

.videoplayer span,
.audioplayer span, 
.imageview span {
    color: #fff;
    z-index: 2000;
    cursor: pointer;
    background: #F00;
    border-radius: 50%;
    padding: 1rem;
}

/** View Box
------------------------------------------ */
.vwb-box {
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	
	position: fixed;
	inset: 0;
	z-index: -100;

	background: #000;
	
	transition: .2s;
	opacity: 0;
	transform: scale(0);
}

.vwb-title {
    display: flex;
    align-items: center;
    gap: 1rem;
    color: #ff0;
}

.vwb-box span {
    cursor: pointer;
    background: rgb(255 0 0 / 39%);
    padding: 0.5rem;
}

.vwb-box video,
.vwb-box audio,
.vwb-box img {
	max-width: 100%;
	max-height: calc(100vh - 116px);
	
	transition: 0.3s 0.2s;
	opacity: 0;
	transform: scale(0);
	height: 0;
}

.vwb-box.on {
	z-index: 10000;
	transform: none;
	opacity: 1;
}

.vwb-box.on.video video,
.vwb-box.on.audio audio,
.vwb-box.on.img img {
	transform: none;
	opacity: 1;
	height: auto;
}

.vwb-box.on.audio audio {
	height: 30px;
}

</style>
</head>
<body>

	<header>
		<div class="container">
			<span class="material-symbols-outlined">public</span>
			<h1>File Explorer</h1>        
		</div>
	</header>

	<main>
		<div class="container">
			<h3><?php echo $breadcumbs?></h3>
			<ul class="files">
				<?php echo $files?>
			</ul>
		</div>
	</main>

	<footer>
		<div class="container">
			<ul class="bmenu">
				<li id="mnu-home"><span class="material-symbols-outlined">home</span><span>Home</span></li>
				<li id="mnu-video"><span class="material-symbols-outlined">publish</span><span>UpLoad</span></li>
				<li><span class="material-symbols-outlined">delete</span><span>Excluir</span></li>
				<li id="mnu-back"><span class="material-symbols-outlined">arrow_back</span><span>Voltar</span></li>
			</ul>
		</div>
	</footer>

	<div class="videoplayer">
		<span class="material-symbols-outlined">close</span>
		<video id="videoplayer" src="" controls autoplay></video>
	</div>
	<div class="audioplayer">
		<span class="material-symbols-outlined">close</span>
		<audio controls autoplay src=""></audio>
	</div>

	<div class="imageview">
		<span class="material-symbols-outlined">close</span>
		<img src="">
	</div>

	<div class="vwb-box">
		<div class="vwb-title">
			<h3></h3>
			<span class="material-symbols-outlined">close</span>
		</div>		
		<video src="" controls autoplay></video>
		<audio controls autoplay src=""></audio>
		<img src="">
	</div>

	<script>
		let domain = location.origin,
			atualpath = '',
			dir = [],
			vwbBox, vwbVideo, vwbAudio, vwbImg

		const videoX = ['mp4','mkv','mov', 'avi'],
			audioX = ['mp3','aif','wav','ogg','wma','3gp'],
			imageX = ['jpg','png','jpeg','gif', 'webp'],
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
			atualpath = location.search.replace('?path=', '')
			dir = atualpath.split('/')

			vwbBox = __('.vwb-box')
			vwbTitle = __('.vwb-title h3')
			vwbVideo = __('.vwb-box video')
			vwbAudio = __('.vwb-box audio')
			vwbImg = __('.vwb-box img')

			__e(() => goBack(), '#bcb-back') // Breadcumbs back
			__e(() => goHome(), '#mnu-home') // Footer menu home
			__e(() => goBack(), '#mnu-back') // Footer menu back
			__e(() => closeBox(), '.vwb-box span')

			__e(() => testVideo(), '#mnu-video')
		}

		const getDir = (path = '') => domain + dir.join('/') + '/' + path
		const getPath = (path = '') => domain + '?path=' + dir.join('/') + '/' + path
		const getFile = (path = '') => domain + '?file=' + dir.join('/') + '/' + path

		const clickfolder = path => location.href = domain + '?path=' + atualpath + '/' + path

		const clickfile = (path, type) => {
			if(videoX.includes(type)) return playvideo(path)
			if(audioX.includes(type)) return playaudio(path)
			if(imageX.includes(type)) return imageview(path)
			return location.href = getDir(path)
		}

		const voltar = () => {
			alert('Voltar ??')
		}

		const testVideo = () => {
			vwbTitle.innerText = 'lorem-ipsum.jpg'
			//vwbVideo.src = domain + '?video=/Videos/Nikolas_20231018.mp4'
			//vwbAudio.src = domain + '?video=/Grupo Vida - NASCE UMA LUZ - MP3/5B - Senhor Me Alegro Em Ti - Grupo Vida.mp3'
			vwbImg.src = domain + '?file=/Images/lorem-ipsum.jpg'
			
			//vwbAudio.play()

			openBox('img')

		}

		const goBack = () => {
			dir.pop()
			location.href = getPath()
		}
		const goHome = () => location.href = domain

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

		const playvideo = (path) => { console.log(path)
			vwbTitle.innerText = path
			vwbVideo.src = getFile(path)
			vwbVideo.play()
			openBox('video')
		}

		const playaudio = (path) => { console.log(path)
			vwbTitle.innerText = path
			vwbAudio.src = getFile(path)
			vwbAudio.play()
			openBox('audio')
		}

		const imageview = (path) => { console.log(path)
			vwbTitle.innerText = path
			vwbImg.src = getFile(path)
			openBox('img')
		}
	</script>
</body>
</html>