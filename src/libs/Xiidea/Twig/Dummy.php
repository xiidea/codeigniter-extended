<?php

/*
 * This file is part of the CIX package.
 *
 * (c) Roni Saha <roni.cse@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Xiidea\Twig;

class Dummy
{
    public function __call($a, $b)
    {
        error_log('Twig is not enabled. Add $config[\'enable_twig\'] = true In config.php');
        show_error('Twig is not enabled. Add $config[\'enable_twig\'] = true In config.php');
    }
}