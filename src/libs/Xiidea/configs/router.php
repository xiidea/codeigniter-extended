<?php
/*
 * This file is part of the CIX package.
 *
 * (c) Roni Saha <roni.cse@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/*
 * This file implements rewrite rules for PHP built-in web server.
 *
 * See: http://www.php.net/manual/en/features.commandline.webserver.php
 *
 * If you have custom directory layout, then you have to write your own router
 * and pass it as a value to 'router' option of server:run command.
 *
 * @author: Michał Pipa <michal.pipa.xsolve@gmail.com>
 * @author: Albert Jessurum <ajessu@gmail.com>
 * @author: Roni Saha <roni.cse@gmail.com>
 */

if (is_file($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$_SERVER['SCRIPT_NAME'])) {
    return false;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PATH_INFO'] = $_SERVER['PHP_SELF'];
$_SERVER['PHP_SELF'] = '/index.php' . $_SERVER['PHP_SELF'];
$_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'index.php';

require $_SERVER['SCRIPT_FILENAME'];
