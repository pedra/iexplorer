<?php
/* UTILS
------------------------------------------------------------------------------*/
function human_filesize($bytes, $decimals = 2)
{
	if ($bytes == 0) return '0 B';
	$factor = floor((strlen($bytes) - 1) / 3);
	if ($factor > 0) $sz = 'KMGT';
	return sprintf("%.{$decimals}f&nbsp;", 
		$bytes / pow(1024, $factor)) . 
		($sz[$factor - 1] ?? '') . 
		'B';
}

function download($file, $download = true)
{
	$filename = basename($file);
	$ext = explode('.', $filename);
	$ext = strtolower(end($ext));

	if ($ext == 'css')
		$mimeType = 'text/css';
	else if ($ext == 'js')
		$mimeType = 'text/javascript';
	else if ($ext == 'svg')
		$mimeType = 'image/svg+xml';
	else {
		$mimeType = @mime_content_type($file);
		$mimeType = $mimeType === false ? 'application/octet-stream' : $mimeType;
	}

	if ($download) {
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

function debug($var, $exit = false)
{
	$o = '<div><b>' . date("Y-m-d H:i:s") . '</b><br><pre style="border:1px solid #000">' . print_r($var, true) . '</pre></div>';
	file_put_contents(PATH_ROOT . '/debug.html', $o, FILE_APPEND);
	if ($exit) exit(print_r($var, true));
}

function e($o = null, $exit = false)
{
	echo "<pre>" . print_r($o, 1) . "</pre>";
	$exit && exit();
}

function myException($exception)
{
	ob_end_clean();
	echo "<b>Exception:</b> " . $exception->getMessage();
}

function exceptions_error_handler($severity, $message, $filename, $lineno) {
	ob_end_clean();
    throw new ErrorException($message, 0, $severity, $filename, $lineno);
}