<?php
/**
 * Simple script to combine and compress JS files, to reduce the number of file request the server has to handle.
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
header('Content-type: text/javascript');

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
	if (substr($file, -3) !== '.js')
	{
		$file .= '.js';
	}
	return $file;
}

// Calculate an unique ID of requested files & their change time
$files = array_map('addExtension', explode(',', $_GET['files']));
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

    function fileLocator($file =null, $maxDepth = 10, $currentDir = ".")
    {
        if(empty($file)){
            return false;
        }elseif(file_exists($currentDir . "/$file")){
            return $currentDir;
        }elseif(--$maxDepth){
            return fileLocator($file, $maxDepth, $currentDir . "/..");
        }else{
            return false;
        }
    }

    if(file_exists("../../app.config.php")){
        !defined('CIX_APP_CONFIG_LOADER') and define('CIX_APP_CONFIG_LOADER', true);
        require_once("../../app.config.php");
        $baseDir = "../../" . PROJECT_BASEPATH;
    }else{
        $baseDir = fileLocator('composer.json');
    }

    if($baseDir){
        require_once($baseDir . "/src/libs/Xiidea/Helper/JSMinException.php");
        require_once($baseDir . "/src/libs/Xiidea/Helper/JSMin.php");
        $content = \Xiidea\Helper\JSMin::minify($content);
    }

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