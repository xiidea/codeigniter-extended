<?php

/*
 * This file is part of the CIX package.
 *
 * (c) Roni Saha <roni.cse@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Xiidea\Base;

/**
 * CodeIgniter-Extended Config Class
 *
 * This class contains functions that enable config files to be managed
 *
 * @package		CodeIgniter-Extended
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Roni Saha <roni.cse@gmail.com>
 */

class Config extends \CI_Config {

    public function site_url($uri = '')
    {
        if (is_array($uri))
        {
            $uri = implode('/', $uri);
        }

        if (function_exists('get_instance'))
        {
            $CI =& get_instance();
            $uri = $CI->lang->localized($uri);
        }

        return parent::site_url($uri);
    }
}
