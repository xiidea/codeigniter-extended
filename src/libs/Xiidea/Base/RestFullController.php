<?php

namespace Xiidea\Base;

class RestFullController extends Controller
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
/* Location: ./Xiidea/Base/RestController.php */