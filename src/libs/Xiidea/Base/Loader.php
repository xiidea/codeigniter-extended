<?php

namespace Xiidea\Base;

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
