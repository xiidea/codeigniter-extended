<?php

/*
 * This file is part of the CIX package.
 *
 * (c) Roni Saha <roni.cse@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
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