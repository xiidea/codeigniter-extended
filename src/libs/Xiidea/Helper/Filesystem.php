<?php

/*
 * This file is part of the CIX package.
 *
 * (c) Roni Saha <roni.cse@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Xiidea\Helper;

class Filesystem {
    public static function getRelativePath( $path, $compareTo ) {
        $path = str_replace('\\','/',$path);
        $compareTo = str_replace('\\','/',$compareTo);

        if ( substr( $path, -1 ) == '/' ) {
            $path = substr( $path, 0, -1 );
        }
        if ( substr( $path, 0, 1 ) == '/' ) {
            $path = substr( $path, 1 );
        }

        if ( substr( $compareTo, -1 ) == '/' ) {
            $compareTo = substr( $compareTo, 0, -1 );
        }
        if ( substr( $compareTo, 0, 1 ) == '/' ) {
            $compareTo = substr( $compareTo, 1 );
        }

        // simple case: $compareTo is in $path
        if ( strpos( $path, $compareTo ) === 0 ) {
            $offset = strlen( $compareTo ) + 1;
            return substr( $path, $offset );
        }

        $relative  = array(  );
        $pathParts = explode( '/', $path );
        $compareToParts = explode( '/', $compareTo );

        foreach( $compareToParts as $index => $part ) {
            if ( isset( $pathParts[$index] ) && $pathParts[$index] == $part ) {
                continue;
            }

            $relative[] = '..';
        }

        foreach( $pathParts as $index => $part ) {
            if ( isset( $compareToParts[$index] ) && $compareToParts[$index] == $part ) {
                continue;
            }

            $relative[] = $part;
        }

        return implode( '/', $relative );
    }

    public static function copyDirectory( $source, $destination ) {
        if ( is_dir( $source ) ) {
            @mkdir( $destination );
            $directory = dir( $source );
            while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
                if ( $readdirectory == '.' || $readdirectory == '..' ) {
                    continue;
                }
                $PathDir = $source . '/' . $readdirectory;
                if ( is_dir( $PathDir ) ) {
                    self::copyDirectory( $PathDir, $destination . '/' . $readdirectory );
                    continue;
                }
                copy( $PathDir, $destination . '/' . $readdirectory );
            }

            $directory->close();
        }else {
            copy( $source, $destination );
        }
    }

    public static function removeBasePath($path, $basePath="")
    {
        if (substr($path, 0, strlen($basePath)) == $basePath) {
            $path = substr($path, strlen($basePath));
        }

        return $path;
    }

    /**
     * @param string $d directory to search
     * @param string $pre prepend the directory name to form unique file name
     * @param string $ext
     * @return array
     */
    public static function scanForFiles($d="",$pre="", $ext = 'twig'){
        $files = array();
        $dir=array();
        $more_files=array();
        foreach (new \DirectoryIterator($d) as $file) {
            if($file->isDir()){
                if(!$file->isDot()){
                    $dir[] =(string) $file;
                }
            }else{
                (preg_match('/^.*\.('.$ext.')$/i', $file)) AND $files[] = "$pre".$file->getFilename();
            }
        }
        if(!empty($dir)){
            foreach($dir as $dname){
                $more_files= array_merge(self::scanForFiles($d.DIRECTORY_SEPARATOR.$dname,"$dname/", $ext),$more_files);
            }

        }
        return array_merge($files,$more_files);
    }

    public static function fileLocator($file =null, $maxDepth = 10, $currentDir = ".")
    {
        if(empty($file)){
            return false;
        }elseif(file_exists($currentDir . "/$file")){
            return $currentDir;
        }elseif(--$maxDepth){
            return self::fileLocator($file, $maxDepth, $currentDir . "/..");
        }else{
            return false;
        }
    }

    public function exists($path)
    {
       return file_exists($path);
    }

    public function remove($dir)
    {
        $it = new \RecursiveDirectoryIterator($dir);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach($files as $file) {
            if ($file->getFilename() === '.' || $file->getFilename() === '..') {
                continue;
            }
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($dir);
    }

    public function clear($dir)
    {
        $this->remove($dir);
        mkdir($dir, 0, true);
    }
}