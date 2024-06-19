<?php

include_once __DIR__ . "/inc/utils.php";
include_once __DIR__ . "/inc/start.php";

/**
 * TODO: add micro Router
 * 
 * 1 - Scan directory: localhost/sc/<path-to-directory> (GET: relative to root path)
 * 2 - Download file: localhost/dw/<path-to-file> (GET: pdf/docx/xls/ppt/css/html/etc)
 * 3 - Stream file: localhost/st/<path-to-file> (GET: audio/video/image)
 * 4 - Create file/directory: localhost/md | localhost/mf (POST + file upload)
 * 5 - Delete file/directory: localhost/dd | localhost/df (DELETE)
 * 
 		 	Future Features
 			===============
 * 6 - Manager Personal DB Interface: localhost/mdb 
 		(GET: list of tables, POST: create table, DELETE: drop table)
 * 
 * 7 - Update audio/video player and image preview (future features)
		Let's build a beautiful home media center
 */


// ROUTER ----------------------------------------------------------------------
//(new Lib\Router())
	// ->all('m/(.*)|m', '\Module\Message\Router')
	// ->all('u/(.*)|u', '\Module\User\Router')
	// ->all('f/(.*)|f', '\Module\File\Router')

	//->get('meta/', '\Module\Utils\Metatag', 'getMeta')

	//->resolve()
	// ->e($_SERVER)
	// ->e(["REQUEST_URI" => $_SERVER["REQUEST_URI"], "PATH_INFO" => $_SERVER["PATH_INFO"]])
	// ->e(null, true);

	//->e(null, true)

	//->run();




/* DOWNLOAD
------------------------------------------------------------------------------*/
if(trim($_SERVER['REQUEST_URI'], '/') != '' && count($_GET) == 0 ) 
{
	$file = urldecode(__DIR__ . '/public' . $_SERVER['REQUEST_URI']);
	if(is_file($file)) download($file, false);
	
	$file = urldecode(ROOT . trim($_SERVER['REQUEST_URI'], '/'));
	if(is_file($file)) download($file);

	exit('<pre>'.print_r($o, true).'</pre>');

	header('Location: /');
	exit;
}

/* STREAM FILES
----------------------------------------------------------------------------- */
if(isset($_GET['file']) && $_GET['file'] != ""){
	$file = realpath(ROOT . $_GET['file']);
	if(is_file($file) && str_starts_with($file, ROOT)) {
		$stm = new Lib\Stream($file);
		$stm->start();
	}
	exit;
}

/* SCAN PATH
------------------------------------------------------------------------------*/
if(isset($_GET['scan']) && $_GET['scan'] != "") 
{
	$dir = realpath(ROOT . $_GET['scan']);
	if(is_dir($dir) && 
		str_starts_with($dir, ROOT)) 
			$scandir = new Lib\ScanDir($dir);
			$scandir->scan();
	
	header("HTTP/1.0 404 Not Found");
	exit;
}

include_once __DIR__ . '/inc/index.html';