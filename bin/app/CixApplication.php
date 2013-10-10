<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Only For Me
 * Date: 10/10/13
 * Time: 8:17 AM
 * To change this template use File | Settings | File Templates.
 */

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use Xiidea\Helper\ConfigResolver;

class CixApplication extends Application {

    public function __construct(ConfigResolver $kernel)
    {
        parent::__construct('Cdeigniter Extended', $kernel->getVersion() . '-' . $kernel->getEnvironment().($kernel->isDebug() ? '/debug' : ''));
        $this->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', $kernel->getEnvironment()));
        $this->getDefinition()->addOption(new InputOption('--no-debug', null, InputOption::VALUE_NONE, 'Switches off debug mode.'));
    }
}