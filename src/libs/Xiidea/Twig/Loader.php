<?php
/**
 * @Author: Roni Kumar Saha
 *        Date: 7/17/13
 *        Time: 3:33 PM
 */

namespace Xiidea\Twig;


use Xiidea\Base\Controller;

class Loader extends \Twig_Environment
{

    /**
     * @var Controller
     */
    private $CI;

    public function __construct(\Twig_LoaderInterface $loader = NULL, $options = array())
    {
        parent::__construct($loader, $options);

        $this->CI = & get_instance();

        if(isset($options['debug']) && $options['debug']){
            $this->addExtension(new \Twig_Extension_Debug());
        }

        $this->initCustomExtensions();

    }

    private function initCustomExtensions()
    {
        $this->addExtension(new TwigCIXExtension($this->CI));
        $this->addExtension(new TwigEzRbacExtension($this->CI));
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
     * Displays a template.
     *
     * {@inheritdoc }
     */
    public function display($name, array $context = array())
    {
        $name = $this->CI->getTwigTemplateName($name);
        $context['__FILE__'] = $this->CI->getTwigPath($name);

        $output = $this->loadTemplate($name)->render($context);

        $this->CI->output->append_output($output);
    }
}