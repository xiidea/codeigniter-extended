<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Only For Me
 * Date: 9/14/13
 * Time: 7:43 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Xiidea\Twig;


class Proxy {

    private $env;

    public function __construct(Loader $environment)
    {
        $this->env = $environment;
    }

    public function __call($method, $args = array())
    {
        if(is_callable($method)){
            return call_user_func_array($method, $args);
        }

        if(!$this->env->isDebug()){
            return null;
        }
        $trace = debug_backtrace(null, 2);

        $errMsg = 'Called to an undefined function : <b>' . $method . "</b> ";

        if(!empty($trace[1]['args'])){
            $errMsg .= ' in ' . $trace[1]['args'][0];
        }

        trigger_error($errMsg, E_USER_NOTICE);
        return NULL;
    }
}