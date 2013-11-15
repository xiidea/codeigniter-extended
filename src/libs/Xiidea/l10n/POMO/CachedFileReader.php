<?php

/*
 * This file is part of the CIX package.
 *
 * (c) Roni Saha <roni.cse@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Xiidea\l10n\POMO;


class CachedFileReader extends StringReader
{
    function __construct($filename)
    {
        parent::__construct();

        $this->_str = file_get_contents($filename);

        if (FALSE === $this->_str){
            return FALSE;
        }

        $this->_pos = 0;
    }
}