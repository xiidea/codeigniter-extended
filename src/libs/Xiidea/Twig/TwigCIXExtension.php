<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Only For Me
 * Date: 9/8/13
 * Time: 10:21 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Xiidea\Twig;


class TwigCIXExtension extends \Twig_Extension{

    /**
     * @var \CI_Controller
     */
    private $CI;

    /**
     * @var Loader
     */
    private $env;

    public function __construct($ci, Loader $environment)
    {
        $this->CI = $ci;
        $this->env = $environment;
    }

    public function getGlobals()
    {
        return array(
            'APPPATH' => realpath(APPPATH),
            'DIRECTORY_SEPARATOR' => DIRECTORY_SEPARATOR,
            'fn' => new Proxy($this->env),
            'controller' => $this->getController(),
        );
    }

    private function getController()
    {
        $reflector = new \ReflectionClass(get_class($this->CI));
        return $reflector->getFileName();
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
            new \Twig_SimpleFunction('anchor', 'anchor', array('is_safe' => array('html')))
        );
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
