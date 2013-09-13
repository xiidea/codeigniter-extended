<?php
/**
 * @Author: Roni Kumar Saha
 * Date: 7/17/13
 * Time: 12:58 PM
 */

namespace Xiidea\Twig;


class Dummy
{
    public function __call($a,$b)
    {
        error_log('Twig is not enabled. Add $config[\'enable_twig\'] = true In config.php');
        show_error('Twig is not enabled. Add $config[\'enable_twig\'] = true In config.php');
    }
}