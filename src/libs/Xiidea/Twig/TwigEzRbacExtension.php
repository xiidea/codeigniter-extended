<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Only For Me
 * Date: 9/8/13
 * Time: 10:21 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Xiidea\Twig;


class TwigEzRbacExtension extends \Twig_Extension{

    /**
     * @var \CI_Controller
     */
    private $CI;

    public function __construct($ci)
    {
        $this->CI = $ci;
    }

    public function getName()
    {
        return 'cix_twig_ezrbac';
    }

    public function getGlobals()
    {
        return array(
            'app' => array(
                        'user'=>$this->CI->ezrbac->getCurrentUser(),
                        'session'=>$this->CI->session->all_userdata()
                        ),
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('logout_url', array($this->CI->ezuri, 'logout'))
        );
    }
}
