<?php
/**
 * @Author: Roni Kumar Saha
 * Date: 7/16/13
 * Time: 11:49 AM
 */

namespace Xiidea\l10n\POMO;


class CachedFileReader extends StringReader {
    function __construct($filename) {
        parent::__construct();
        $this->_str = file_get_contents($filename);
        if (false === $this->_str)
            return false;
        $this->_pos = 0;
    }
}