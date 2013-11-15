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
 * Loader Class
 *
 * Loads views and files
 *
 * @package		CodeIgniter-Extended
 * @subpackage	Libraries
 * @category	Loader
 * @author		Roni Saha <roni.cse@gmail.com>
 */
class Loader extends \CI_Loader
{
    protected $CI;

    public function __construct()
    {
        parent::__construct();
        $this->CI =& get_instance();
    }

    public function viewWithLayout($view, $data = null, $return = false)
    {
        $data['content'] = $this->view($view, $data, true);

        return $this->view("_layouts/" . $this->CI->get_layout(), $data, $return);
    }
}
