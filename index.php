<?php
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
//error_reporting(E_ALL);

define('SEP', DIRECTORY_SEPARATOR);
define('ROOT', realpath('F:\\'));
define('IGNORE', ['.', '..', 'server.log', 'index.php', '_start_.bat']);

/* DOWNLOAD
------------------------------------------------------------------------------*/
if(trim($_SERVER['REQUEST_URI'], '/') != '' && count($_GET) == 0 ) 
{
	$file = realpath(urldecode(__DIR__ . '/public' . $_SERVER['REQUEST_URI']));
	if(is_file($file)) download($file, false);
	
	$file = urldecode(ROOT . trim($_SERVER['REQUEST_URI'], '/'));
	if(is_file($file)) download($file);

	header('Location: /');
	exit;
}

/* STREAM FILES
----------------------------------------------------------------------------- */
if(isset($_GET['file']) && $_GET['file'] != ""){
	$file = realpath(ROOT . $_GET['file']);
	if(is_file($file) && str_starts_with($file, ROOT)) {
		include_once './inc/stream.php';
		$stm = new Stream($file);
		$stm->start();
	}
	exit;
}

/* SCAN PATH
------------------------------------------------------------------------------*/
if(isset($_GET['scan']) && $_GET['scan'] != "") 
{
	$scan = realpath(ROOT . $_GET['scan']);
	if(is_dir($scan) && str_starts_with($scan, ROOT)) 
	{
		$dd = [];
		$df = [];
		$s = scandir($scan);
		foreach($s as $d){
			if(in_array($d, IGNORE)) continue;
				
			if(is_dir($scan.'/'.$d)) {
				array_push($dd, $d);
			} else {
				array_push($df, [
					"name" => $d, 
					"size" => human_filesize(filesize($scan.'/'.$d)),
					"ext" => strtolower(pathinfo($scan.'/'.$d)['extension'] ?? '')
				]);
			}	
		}

		header('Content-Type: application/json');
		exit(json_encode([
			"dir" => $dd,
			"file" => $df
		]));
	}
	header("HTTP/1.0 404 Not Found");
	exit;
}

/* UTILS
------------------------------------------------------------------------------*/
function human_filesize($bytes, $decimals = 2) {
	$factor = floor((strlen($bytes) - 1) / 3);
	if ($factor > 0) $sz = 'KMGT';
	return sprintf("%.{$decimals}f&nbsp;", $bytes / pow(1024, $factor)) . @$sz[$factor - 1] . 'B';
}

function download ($file, $download = true) 
{
	$filename = basename($file);
	$ext = explode('.', $filename);
	$ext = strtolower(end($ext));

	if($ext == 'css') $mimeType = 'text/css';
	else if($ext == 'js') $mimeType = 'text/javascript';
	else if($ext == 'svg') $mimeType = 'image/svg+xml';
	else {
		$mimeType = @mime_content_type($file);
		$mimeType = $mimeType === false ? 'application/octet-stream' : $mimeType;
	}

	if($download) {
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
	}
	header("Content-Type: $mimeType");
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file));
	flush();
	readfile($file);

	exit();
}

include_once './inc/index.html';