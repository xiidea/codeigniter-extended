<?php
/**
 * @Author: Roni Kumar Saha
 *        Date: 7/17/13
 *        Time: 3:33 PM
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

    public function __construct(\Twig_LoaderInterface $loader = NULL, $options = array())
    {
        parent::__construct($loader, $options);

        $this->CI = & get_instance();

        if (isset($options['debug']) && $options['debug']) {
            $this->addExtension(new \Twig_Extension_Debug());
        }

        $this->assetsDir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;
        $this->assetFactory = new AssetFactory($this->assetsDir, $this->isDebug());

        $this->initCustomExtensions();
    }

    private function initCustomExtensions()
    {
        $this->addExtension(new TwigCIXExtension($this->CI));
        $this->addExtension(new TwigEzRbacExtension($this->CI));
        $this->addExtension(new AsseticExtension($this->assetFactory));

        $extensions = $this->CI->config->item('twig_extensions');

        foreach (array($extensions) as $extension) {
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
        $name = $this->CI->getTwigTemplateName($name);
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
