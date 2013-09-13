<?php
/**
 * @Author: Roni Kumar Saha
 *        Date: 9/7/13
 *        Time: 7:45 AM
 */

namespace Xiidea\Installer\Services;


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
            self::$connection      = @mysql_connect(self::$config['hostname'],
                                                    self::$config['username'],
                                                    self::$config['password'])
                                                or (self::$connectionError = mysql_error() and FALSE);

        }
    }

    private static function create($dbName)
    {
        if (!self::$connection) {
            return FALSE;
        }

        mysql_query("CREATE DATABASE `$dbName`", self::$connection);
        self::$newDatabase = TRUE;
        return self::select($dbName);
    }

    public function isNewDatabase()
    {
        return self::$newDatabase;
    }

    private static function select($db, $create = FALSE)
    {
        return self::$connection and mysql_select_db($db, self::$connection) or ($create and self::create($db));
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
        return (mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $table . "'")) == 1);
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

            mysql_query($query) or $error[] = mysql_error();
        }
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