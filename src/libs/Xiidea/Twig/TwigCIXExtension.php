<?php

/*
 * This file is part of the CIX package.
 *
 * (c) Roni Saha <roni.cse@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Xiidea\Twig;

class TwigCIXExtension extends \Twig_Extension
{

    /**
     * @var \CI_Controller
     */
    private $CI;

    /**
     * @var Loader
     */
    private $env;

    /**
     * @var string
     */
    private $_controller_path;

    public function __construct($ci)
    {
        $this->CI = $ci;
    }

    /**
     * Initializes the runtime environment.
     *
     * This is where you can load some file that contains filter functions for instance.
     *
     * @param \Twig_Environment $environment The current Twig_Environment instance
     */
    public function initRuntime(\Twig_Environment $environment){
        $this->env = $environment;
    }

    public function getGlobals()
    {
        return array(
            'APPPATH' => realpath(APPPATH),
            'DIRECTORY_SEPARATOR' => DIRECTORY_SEPARATOR,
            'controller' => $this->getController(),
        );
    }

    private function getController()
    {
        if(!$this->_controller_path){
            $reflector = new \ReflectionClass(get_class($this->CI));
            $this->_controller_path = $reflector->getFileName();
        }

        return $this->_controller_path;
    }

    public function getName()
    {
        return 'cix_twig_extension';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('_t', '_t'),
            new \Twig_SimpleFunction('nonce', 'nonce'),
            new \Twig_SimpleFunction('valid_nonce', 'valid_nonce'),
            new \Twig_SimpleFunction('php_*', array($this,'phpFunctions'),array('is_safe' => array('html'))),
        );
    }

    public function phpFunctions()
    {
        $arg_list = func_get_args();
        $function = array_shift($arg_list);

        if(is_callable($function)){
            return call_user_func_array($function, $arg_list);
        }

        if(!$this->env->isDebug()){
            return null;
        }

        $trace = debug_backtrace(null, 2);

        $debugInfo = $this->getDebugInfo($trace);

        $errMsg = 'Called to an undefined function : <b>php_' . $function . "</b> ";

        if(isset($debugInfo['file'], $debugInfo['line'])){
            _exception_handler(E_USER_NOTICE, $errMsg, $debugInfo['file'], $debugInfo['line']);
        }else{
            trigger_error($errMsg, E_USER_NOTICE);
        }

        return NULL;
    }

    protected function getDebugInfo($trace)
    {
        $class = $trace[1]['class'];
        $obj = new $class($this->env);
        $return['file'] = $obj->getTemplateName();
        $debugInfo = $obj->getDebugInfo();
        $return['line'] = $debugInfo[$trace[0]['line']-1];
        return $return;
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(new \Twig_SimpleFilter('localize', '_t'));
    }

}
