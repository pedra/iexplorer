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
(new Lib\Router())
	->get('/', 				'\Module\Page', 'home')

	->get('asset/(.*)', 	'\Module\File', 'asset')
	->get('file/(.*)', 		'\Module\File', 'download')
	->get('stream/(.*)', 	'\Module\File', 'stream')

	->get('scan/(.*)|scan', '\Module\Scandir', 'get')

	->run();