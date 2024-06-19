<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
set_error_handler('exceptions_error_handler');

header("Access-Control-Allow-Origin: *");
// header("Content-Security-Policy: connect-src *; default-src *");

define('SEP', DIRECTORY_SEPARATOR);
define('PATH_ROOT', dirname(__FILE__));
define('ENV', parse_ini_file(PATH_ROOT . "/.env"));

// Relative to Scan access
define('ROOT', realpath('F:\\'));
define('IGNORE', ['.', '..', 'server.log', 'index.php', '_start_.bat']);

// AUTO LOADER -----------------------------------------------------------------
spl_autoload_register(function ($class) {
	$path = PATH_ROOT . '/' . strtolower(str_replace('\\', '/', $class) . '.php');
	if (file_exists($path)) include_once $path;
});

// CACHE & SESSION -------------------------------------------------------------
// ob_start("ob_gzhandler");
// session_name("plm45022");
// session_start();