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
 * CodeIgniter-Extended Application Controller Base Class For RestFull Request
 *
 * @package		CodeIgniter-Extended
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Roni Saha <roni.cse@gmail.com>
 */
class RestFullBase extends \CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->_restrictFromRouter(__CLASS__);
    }

    public function sendResponse($status = 200, $body = null, $content_type = 'application/json')
    {
        set_status_header($status);

        if (!empty($body)) {

            header('Content-type: ' . $content_type);
            die($body);

        } else {

            switch ($status) {
                case 401:
                    $message = 'You must be authorized to view this page.';
                    break;
                case 404:
                    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
                default :
                    $message = 'An Error Was Encountered!';
            }

            $this->show_error($message, $status);
        }
    }

    public function show_error($msg = null, $code = null, $header = null)
    {
        if ($this->_isAjaxRequest) {
            $msg = json_encode(array('success' => false, 'msg' => $msg));
            set_status_header($code);
            die($msg);
        }
        if ($code == 404) {
            show_404();
        }

        show_error($msg, $code, $header);
    }

    protected function _restrictFromRouter($class)
    {
        if (in_array($this->_method, get_class_methods($class))) {
            $this->show_error(null, 404);
        }
    }
}

/* End of file RestFullController.php */
/* Location: ./Xiidea/Base/RestFullBase.php */