<?php

namespace Xiidea\Base;

use Xiidea\Twig\Dummy;
use Xiidea\Twig\Loader;

/**
 * @property \CI_DB_active_record $db
 * @property \CI_DB_forge $dbforge
 * @property \CI_Benchmark $benchmark
 * @property \CI_Calendar $calendar
 * @property \CI_Cart $cart
 * @property \CIX_Config $config
 * @property \CIX_Controller $controller
 * @property \CI_Email $email
 * @property \CI_Encrypt $encrypt
 * @property \CI_Exceptions $exceptions
 * @property \CI_Form_validation $form_validation
 * @property \CI_Ftp $ftp
 * @property \CI_Hooks $hooks
 * @property \CI_Image_lib $image_lib
 * @property \CI_Input $input
 * @property \CIX_Lang $lang
 * @property \CIX_Loader $load
 * @property \CI_Log $log
 * @property \CIX_Model $model
 * @property \CI_Output $output
 * @property \CI_Pagination $pagination
 * @property \CI_Parser $parser
 * @property \CI_Profiler $profiler
 * @property \CI_Router $router
 * @property \CI_Session $session
 * @property \CI_Sha1 $sha1
 * @property \CI_Table $table
 * @property \CI_Trackback $trackback
 * @property \CI_Typography $typography
 * @property \CI_Unit_test $unit_test
 * @property \CI_Upload $upload
 * @property \CI_URI $uri
 * @property \CI_User_agent $user_agent
 * @property \CI_Xmlrpc $xmlrpc
 * @property \CI_Xmlrpcs $xmlrpcs
 * @property \CI_Zip $zip
 * @property \CI_Javascript $javascript
 * @property \CI_Jquery $jquery
 * @property \CI_Utf8 $utf8
 * @property \CI_Security $security
 */

class Controller extends \CI_Controller
{

    protected $_layout = 'main';
    private $_isAjaxRequest = false;
    private $_twig = null;
    private $_twigPath = null;
    private $_method = null;
    public $twig;

    function __construct()
    {
        parent::__construct();
        $this->_method = $this->router->fetch_method();
        $this->_restrictFromRouter();

        $this->_isAjaxRequest = ($this->input->get('ajax') || $this->input->is_ajax_request());
    }

    public function get_layout()
    {
        return $this->_layout;
    }

    public function set_layout($layout)
    {
        return $this->_layout = $layout;
    }

    public function render($template = null, $data = array(), $return = false)
    {
        $this->_normalizeArguments($template, $data, $return, $isTwig);

        if ($isTwig || self::isTwigTemplate($template)) {
            $twig = $this->twig();

            $output = $twig->render($template, $data);

            if ($return) {
                return $output;
            }

            $this->output->append_output($output);

        } else {
            if ($this->isAjax()) {
                return $this->load->view($template, $data, $return);
            } else {
                return $this->load->viewWithLayout($template, $data, $return);
            }
        }
    }

    private function _normalizeArguments(&$template, &$data, &$return, &$isTwig = false)
    {
        if (!is_string($template) || $template === "" || $template === null) {

            $_template = $this->_getCurrentPathTemplate($this->_twigEnabled(), $isTwig);

            if (is_array($template)) {

                if (is_bool($data)) {
                    $return = $data;
                }

                $data = $template;

            } elseif (is_bool($template)) {
                $return = $template;
            }

            $template = $_template;
        }else{
            if(is_bool($data)){
                $return = $data;
                $data = array();
            }
        }
    }

    private function _getCurrentPathTemplate($twig = false, &$isTwig = false)
    {
        $twig = ($twig && $this->_twigEnabled());

        $controller_name = $this->router->fetch_class();
        $templatePath = $this->router->fetch_directory() . $controller_name . "/" . $this->_method;

        if ($twig && $this->twig()->getLoader()->exists($templatePath . ".twig")) {
            $isTwig = true;
            return $templatePath . ".twig";
        } else {
            return $templatePath;
        }
    }

    private function _twigEnabled()
    {
        return ($this->twig() instanceof \Twig_Environment);
    }

    /**
     * @return Loader
     */
    public function twig()
    {
        if (!$this->_twig) {
            $this->_twigInit();
        }

        return $this->_twig;
    }

    private function _twigInit()
    {
        if ($this->config->item('enable_twig')) {
            $twigDirectory = $this->config->item('twig_dir');
            if (!$twigDirectory) {
                $twigDirectory = 'twig';
            }

            $templatesPath = APPPATH . $twigDirectory;

            $this->_twigPath = realpath($templatesPath);

            $extensions = $this->config->item('twig_extensions');

            if (class_exists('\Twig_Loader_Filesystem')) {
                $loader = new \Twig_Loader_Filesystem($templatesPath);
                $this->_twig = new Loader($loader, array(
                                                    'debug' => ENVIRONMENT != 'production',
                                                    'cache' => APPPATH . 'cache'
                                                    ), $this, $extensions);
            } else {
                show_error('Twig is not installed. Install Twig first by run the command "composer update twig/twig"');
            }
        } else {
            $this->_twig = new Dummy();
        }
    }

    public static function isTwigTemplate($template)
    {
        return substr(strrchr($template, '.'), 1) == 'twig';
    }

    public function getTwigPath($template = null)
    {
        if ($template == null){
            return $this->_twigPath;
        }

        $appendExtension = self::isTwigTemplate($template) ? "" : ".twig";

        return str_replace('/', DIRECTORY_SEPARATOR, $this->_twigPath . DIRECTORY_SEPARATOR . $template . $appendExtension);
    }

    public function getTwigTemplateName($template)
    {
        $appendExtension = self::isTwigTemplate($template) ? "" : ".twig";
        return $template . $appendExtension;
    }

    public function isAjax()
    {
        return $this->_isAjaxRequest;
    }

    public function _render($template, $data = array(), $return = false)
    {
        return $this->load->view($template, $data, $return);
    }

    public function show_error($msg = null, $code = null, $header = null)
    {
        if ($this->_isAjaxRequest) {
            $msg = json_encode(array('success' => false, 'msg' => $msg));
            set_status_header($code);
            die($msg);
        }
        if ($code == 404) {
            show_404();
        }

        show_error($msg, $code, $header);
    }

    private function _restrictFromRouter()
    {
        if(in_array($this->_method, get_class_methods(__CLASS__))){
            $this->show_error(null, 404);
        }
    }
}

/* End of file Controller.php */
/* Location: ./Xiidea/Base/Controller.php */