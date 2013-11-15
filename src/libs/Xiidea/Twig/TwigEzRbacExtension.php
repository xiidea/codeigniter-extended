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

class TwigEzRbacExtension extends \Twig_Extension
{
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
                'user'    => $this->CI->ezrbac->getCurrentUser(),
                'session' => $this->CI->session->all_userdata()
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
