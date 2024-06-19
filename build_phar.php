<?php
// Clean
if (file_exists('iExplorer.phar')) @unlink('iExplorer.phar');
if (file_exists('extract'))	delTree('extract');

// Build
$phar = new Phar('iExplorer.phar', 0, 'iExplorer.phar');
$phar->buildFromDirectory(dirname(__FILE__) . '/app');
$phar->setStub($phar->createDefaultStub('index.php', 'index.php'));

// Verify
//$phar->extractTo(dirname(__FILE__) . '/extract');


// -----------------------------------------------------------------------------
function delTree($dir)
{
	$files = array_diff(scandir($dir), array('.', '..'));
	foreach ($files as $file) {
		(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
	}
	return rmdir($dir);
}