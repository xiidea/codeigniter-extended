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

use Xiidea\Helper\EasyGuide;
/**
 * CodeIgniter-Extended Application Controller Class For RestFull Request
 *
 * @package		CodeIgniter-Extended
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Roni Saha <roni.cse@gmail.com>
 */
class RestFullResourceController extends RestFullBase
{
    protected $isSingleResource = false;

    function __construct()
    {
        parent::__construct();
        $this->_restrictFromRouter(__CLASS__);
        EasyGuide::setRestFullActionMap($this->getRestFullDefinition());
        $this->_re_route();
    }

    public function _remap()
    {
        try {
            call_user_func_array(array($this, $this->router->fetch_method()), array_slice($this->uri->rsegments, 2));
        }
        catch (\Exception $e) {
            $this->sendResponse(404);
        }
    }

    protected function getRestFullDefinition()
    {
        return array();
    }

    private function _re_route()
    {
        $routes = $this->createRestFullRoute();

        $uri = implode('/', $this->uri->rsegments);

        $uri = preg_replace('/index$/', '', $uri);

        if (isset($routes[$uri]))
        {
            return $this->router->_set_request(explode('/', $routes[$uri]));
        }

        foreach ($routes as $key => $val)
        {
            // Convert wild-cards to RegEx
            $key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

            // Does the RegEx match?
            if (preg_match('#^'.$key.'$#', $uri))
            {
                // Do we have a back-reference?
                if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
                {
                    $val = preg_replace('#^'.$key.'$#', $val, $uri);
                }
                return $this->router->_set_request(explode('/', $val));
            }
        }
    }

    private function createRestFullRoute()
    {
        $class = strtolower(get_class($this));

        $resourceFunction = $this->isSingleResource ? 'resource' : 'resources';

        EasyGuide::map(function($r) use ($class, $resourceFunction){
            $r->$resourceFunction($class);
        });

        return EasyGuide::draw();
    }
}

/* End of file RestFullController.php */
/* Location: ./Xiidea/Base/RestFullResourceController.php */