<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Only For Me
 * Date: 10/9/13
 * Time: 10:48 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Xiidea\Commands;


use Symfony\Component\Console\Command\Command;
use Xiidea\Helper\ConfigResolver;

abstract class ConfigAwareCommand extends Command
{
    /**
     * @var ConfigResolver
     */
    private $config;

    public function __construct(ConfigResolver $config)
    {
        $this->config = $config;
        parent::__construct();
    }

    /**
     * @return ConfigResolver
     */
    public function getConfig()
    {
        return $this->config;
    }
}