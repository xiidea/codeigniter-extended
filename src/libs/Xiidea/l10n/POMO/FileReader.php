<?php
/**
 * @Author: Roni Kumar Saha
 * Date: 7/16/13
 * Time: 11:48 AM
 */

namespace Xiidea\l10n\POMO;


class FileReader extends Reader {
    function __construct($filename) {
        parent::__construct();
        $this->_f = fopen($filename, 'rb');
    }

    function read($bytes) {
        return fread($this->_f, $bytes);
    }

    function seekto($pos) {
        if ( -1 == fseek($this->_f, $pos, SEEK_SET)) {
            return false;
        }
        $this->_pos = $pos;
        return true;
    }

    function is_resource() {
        return is_resource($this->_f);
    }

    function feof() {
        return feof($this->_f);
    }

    function close() {
        return fclose($this->_f);
    }

    function read_all() {
        $all = '';
        while ( !$this->feof() )
            $all .= $this->read(4096);
        return $all;
    }
}