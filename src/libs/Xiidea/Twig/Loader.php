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

use Assetic\Extension\Twig\AsseticExtension;
use Assetic\Factory\AssetFactory;
use Xiidea\Base\Controller;

class Loader extends \Twig_Environment
{

    /**
     * @var Controller
     */
    private $CI;
    private $assetsDir;
    private $assetFactory;
    private $_extensions;

    public function __construct(\Twig_LoaderInterface $loader = NULL, $options = array(), $ci = NULL, $extensions = array())
    {
        parent::__construct($loader, $options);

        $this->CI = $ci == NULL ? get_instance() : $ci;

        $this->_extensions = $extensions;

        if (isset($options['debug']) && $options['debug']) {
            $this->addExtension(new \Twig_Extension_Debug());
        }

        $this->assetsDir = rtrim('', DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;
        $this->assetFactory = new AssetFactory($this->assetsDir, $this->isDebug());

        $this->initCustomExtensions();
    }

    private function initCustomExtensions()
    {
        $this->addExtension(new TwigCIXExtension($this->CI));
        $this->addExtension(new TwigEzRbacExtension($this->CI));
        $this->addExtension(new AsseticExtension($this->assetFactory));

        foreach (array($this->_extensions) as $extension) {
            if (empty($extension)) {
                continue;
            }
            $this->addExtension(new $extension($this->CI));
        }
    }

    /**
     * Displays a template.
     *
     * {@inheritdoc }
     */
    public function display($name, array $context = array())
    {
        $this->CI->output->append_output($this->render($name, $context));
    }

    /**
     * Render a template.
     *
     * {@inheritdoc }
     */
    public function render($name, array $context = array())
    {
        $name                = $this->CI->getTwigTemplateName($name);
        $context['__FILE__'] = $this->CI->getTwigPath($name);

        return $this->loadTemplate($name)->render($context);
    }

    /**
     * @return string
     */
    public function getAssetsDir()
    {
        return $this->assetsDir;
    }

    /**
     * @return mixed
     */
    public function getAssetFactory()
    {
        return $this->assetFactory;
    }
}
