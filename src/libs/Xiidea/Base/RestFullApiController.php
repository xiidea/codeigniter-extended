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
 * CodeIgniter-Extended Application Controller Class For RestFull Request
 *
 * @package		CodeIgniter-Extended
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Roni Saha <roni.cse@gmail.com>
 */
class RestFullApiController extends RestFullBase
{

    function __construct()
    {
        parent::__construct();
        $this->_restrictFromRouter(__CLASS__);
        $this->_re_route();
    }

    public function _remap($method, $arguments)
    {
        try {
            call_user_func_array(array($this, $this->router->fetch_method()), $arguments);
        }
        catch (\Exception $e) {
            $this->sendResponse(404);
        }
    }

    private function _re_route()
    {
        $requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
        $controllerMethod = $this->_method . '_' . $requestMethod;

        if (!is_callable(array($this, $controllerMethod))) {
            $this->sendResponse(404);
        }

        $this->router->set_method($controllerMethod);
    }
}

/* End of file RestFullController.php */
/* Location: ./Xiidea/Base/RestFullApiController.php */