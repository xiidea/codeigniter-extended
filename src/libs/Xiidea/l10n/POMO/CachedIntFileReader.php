<?php
/**
 * @Author: Roni Kumar Saha
 * Date: 7/16/13
 * Time: 11:50 AM
 */

namespace Xiidea\l10n\POMO;


class CachedIntFileReader extends CachedFileReader {
    function __construct($filename) {
        parent::__construct($filename);
    }
}