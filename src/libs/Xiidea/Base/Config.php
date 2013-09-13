<?php

namespace Xiidea\Base;

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
