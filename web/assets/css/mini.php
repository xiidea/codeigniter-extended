<?php
/**
 * Simple script to combine and compress CSS files, to reduce the number of file request the server has to handle.
 * For more options/flexibility, see Minify : http://code.google.com/p/minify/
 */

// If no file requested
if (!isset($_GET['files']) or strlen($_GET['files']) == 0)
{
	header('Status: 404 Not Found');
	exit();
}

// Cache folder
$cachePath = 'cache/';
if (!file_exists($cachePath))
{
	mkdir($cachePath);
}

// Tell the browser what kind of data to expect
header('Content-type: text/css');

// Enable compression
if (extension_loaded('zlib'))
{
	header('Content-Encoding: gzip');
	ini_set('zlib.output_compression', 'On');
}

/**
 * Add file extension if needed
 * @var string $file the file name
 * @return string
 */
function addExtension($file)
{
	if (substr($file, -4) !== '.css')
	{
		$file .= '.css';
	}
	return $file;
}

// Calculate an unique ID of requested files & their change time
$files = array_map('addExtension', explode(',', $_GET['files']));

$files = array_unique($files);

$md5 = '';
foreach ($files as $file)
{
	$filemtime = @filemtime($file);
	$md5 .= date('YmdHis', $filemtime ? $filemtime : NULL).$file;
}
$md5 = md5($md5);

// If cache exists of this files/time ID
if (file_exists($cachePath.$md5))
{
	readfile($cachePath.$md5);
}
else
{
	// Load files
	error_reporting(0);
	$content = '';
	foreach ($files as $file)
	{
		$content .= file_get_contents($file);
	}
	
	// Remove comments
	$content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
	
	// Remove tabs, spaces, newlines, etc...
	$content = str_replace(array("\r", "\n", "\t", '  ', '   '), '', $content);
	
	// Delete cache files older than an hour
	$oldDate = time()-3600;
	$cachedFiles = scandir($cachePath);
	foreach ($cachedFiles as $file)
	{
		$filemtime = @filemtime($cachePath.$file);
		if (strlen($file) == 32 and ($filemtime === false or $filemtime < $oldDate))
		{
			unlink($cachePath.$file);
		}
	}
	
	// Write cache file
	file_put_contents($cachePath.$md5, $content);
	
	// Output
	echo $content;
}