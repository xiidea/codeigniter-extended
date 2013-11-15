<?php

/*
 * This file is part of the CIX package.
 *
 * (c) Roni Saha <roni.cse@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
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