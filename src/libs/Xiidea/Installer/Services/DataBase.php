<?php

/*
 * This file is part of the CIX package.
 *
 * (c) Roni Saha <roni.cse@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Xiidea\Installer\Services;

/**
 * Database Class
 *
 * @package		CodeIgniter-Extended
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Roni Saha <roni.cse@gmail.com>
 */

class DataBase
{
    private static $connection;
    private static $config;
    private static $connectionError;
    private static $newDatabase = FALSE;

    public function __construct($dbConfig = array())
    {
        self::connect($dbConfig);
    }

    public function isConnected()
    {
        return self::$connection;
    }

    public function getConnectionError()
    {
        return self::$connectionError;
    }

    private function connect($dbConfig)
    {
        if (!self::$connection) {
            self::$connectionError = "";
            self::$config          = $dbConfig;
            self::$connection      = @mysqli_connect(self::$config['hostname'],
                                                    self::$config['username'],
                                                    self::$config['password'])
                                                or (self::$connectionError = mysqli_error(self::$connection) and FALSE);

        }
    }

    private static function create($dbName)
    {
        if (!self::$connection) {
            return FALSE;
        }

        mysqli_query(self::$connection, "CREATE DATABASE `$dbName`");
        self::$newDatabase = TRUE;
        return self::select($dbName);
    }

    public function isNewDatabase()
    {
        return self::$newDatabase;
    }

    private static function select($db, $create = FALSE)
    {
        return self::$connection and mysqli_select_db(self::$connection, $db) or ($create and self::create($db));
    }

    public function selectDB($db, $create = FALSE)
    {
        return self::select($db, $create);
    }

    public function createDB($db)
    {
        return self::create($db);
    }

    public function checkTable($table)
    {
        return (mysqli_num_rows(mysqli_query(self::$connection, "SHOW TABLES LIKE '" . $table . "'")) == 1);
    }

    public function parseMysqlDump($url)
    {
        if (!file_exists($url)) {
            return false;
        }

        $file_content = file($url);

        $commentFree = array_map(array($this, "stripCommentLines"), $file_content);

        $contents = trim(implode('', $commentFree));

        $file_content = $this->splitQueryText($contents);

        $error = array();
        foreach ($file_content as $query) {
            $query = trim($query);
            if (empty($query)) {
                continue;
            }

            mysqli_query(self::$connection, $this->addTablePrefix($query)) or $error[] = mysqli_error(self::$connection);
        }
    }

    private function addTablePrefix($query)
    {
        if (empty(self::$config['dbprefix'])) {
            return $query;
        }

        return preg_replace(
            '/(insert  into|for the table|for table|CREATE TABLE|DROP TABLE IF EXISTS) `([\w]+)`/',
            '${1} `' . self::$config['dbprefix'] .'${2}`',
            $query
        );

    }

    function splitQueryText($query)
    {
        // the regex needs a trailing semicolon
        $query = trim($query);

        if (substr($query, -1) != ";")
            $query .= ";";

        // i spent 3 days figuring out this line
        preg_match_all("/(?>[^;']|(''|(?>'([^']|\\')*[^\\\]')))+;/ixU", $query, $matches, PREG_SET_ORDER);

        $querySplit = "";

        foreach ($matches as $match) {
            // get rid of the trailing semicolon
            $querySplit[] = substr($match[0], 0, -1);
        }

        return $querySplit;
    }

    function stripCommentLines($in)
    {
        if (substr($in, 0, 2) == "--")
            $in = '';

        return $in;
    }

}