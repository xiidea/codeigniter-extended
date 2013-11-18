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
class RestFullBase extends Controller
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
}

/* End of file RestFullController.php */
/* Location: ./Xiidea/Base/RestFullBase.php */