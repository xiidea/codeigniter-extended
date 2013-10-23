<?php

namespace Xiidea\Helper;

use Xiidea\Helper\Filesystem;

class ConfigResolver
{
    private $version = '1.2.0';
    private $root;
    private $webBasePath;
    private $assetsBasePath;
    private $twigBasePath;
    private $dbConfig;
    private $applicationBasePath;
    private $appConfig;
    private static $environment;


    public function __construct($env = null)
    {
        self::$environment = $this->getFilteredEnvironment($env);
        $this->_initialize();
    }

    private function _initialize()
    {
        $this->root = $this->_stripTrailSlash(realpath(Filesystem::fileLocator('composer.json', 5, __DIR__)));
        $this->_getComposerConfiguration();
        $this->_getApplicationConfig();
        $this->twigBasePath = $this->applicationBasePath . ($this->appConfig['twig_dir'] ? : 'twig');
    }

    private function _getComposerConfiguration()
    {
        $json = json_decode(file_get_contents($this->root . "/composer.json" ));
        $this->applicationBasePath = $this->_stripTrailSlash($this->root . $json->extra->{"ci-app-dir"});
        $this->webBasePath = $this->_stripTrailSlash($this->root . $json->extra->{"ci-web-dir"});
        $this->assetsBasePath = $this->_stripTrailSlash($this->webBasePath . 'assets');
    }

    private function _getApplicationConfig()
    {
        $this->appConfig = $this->_readConfigFile('config.php');
        $this->dbConfig = $this->_readConfigFile('database.php', 'db', 'default');
    }

    private function _readConfigFile($file, $arrayName = 'config', $key = null)
    {
        (! defined('BASEPATH') and define('BASEPATH', true));

        include ($this->applicationBasePath . 'config/'.$file);

        $configuration = $$arrayName;

        return ($key && $configuration[$key]) ? $configuration[$key] : $configuration;
    }

    private function _stripTrailSlash($str)
    {
        return rtrim($str, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * @return mixed
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @return mixed
     */
    public function getWebBasePath()
    {
        return $this->webBasePath;
    }

    /**
     * @return mixed
     */
    public function getApplicationBasePath()
    {
        return $this->applicationBasePath;
    }

    /**
     * @return mixed
     */
    public function getAssetsBasePath()
    {
        return $this->assetsBasePath;
    }

    /**
     * @return mixed
     */
    public function getDbConfig()
    {
        return $this->dbConfig;
    }

    /**
     * @return mixed
     */
    public function getTwigBasePath()
    {
        return $this->twigBasePath;
    }

    /**
     * @return mixed
     */
    public function getAppConfig()
    {
        return $this->appConfig;
    }


    /**
     * @param $key
     *
     * @return mixed
     */
    public function getAppConfigValue($key)
    {
        return isset($this->appConfig[$key]) ? $this->appConfig[$key] : NULL;
    }

    /**
     * @return mixed
     */
    public function getEnvironment()
    {
        if(!self::$environment){
            $bootstrap = $this->webBasePath . 'app.config.php';
            if(file_exists($bootstrap)){
                $data = file_get_contents($bootstrap);
                preg_match("/define\('ENVIRONMENT',(\s)*'([a-z]+)'\);/", $data, $matches);
                if(isset($matches[2])){
                    self::$environment = $matches[2];
                }
            }
            if(!self::$environment){
                self::$environment = 'production';
            }
        }

        return self::$environment;
    }

    public function getEnv()
    {
        $envArray = array(
            'production' => 'prod',
            'development' => 'dev',
            'testing' => 'test'
        );

        return $envArray[$this->getEnvironment()];
    }

    public function isDebug()
    {
        return $this->getEnvironment() == 'development';
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    private function getFilteredEnvironment($env)
    {
        switch($env):
            case 'd':
            case 'dev':
            case 'development':
                $env = 'development';
                break;
            case 'p':
            case 'prod':
            case 'production':
                $env = 'production';
                break;
            case 't':
            case 'test':
            case 'testing':
                $env = 'testing';
                break;
            default:
                $env = null;
        endswitch;
        return $env;
    }

}