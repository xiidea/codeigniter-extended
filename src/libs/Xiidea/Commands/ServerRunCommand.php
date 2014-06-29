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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Runs CIX application using PHP built-in web server
 *
 * @author Michał Pipa <michal.pipa.xsolve@gmail.com>
 * @author Roni Saha <roni.cse@gmail.com>
 */
class ServerRunCommand extends ConfigAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            return false;
        }

        return parent::isEnabled();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('address', InputArgument::OPTIONAL, 'Address:port', 'localhost:8000'),
                new InputOption('docroot', 'd', InputOption::VALUE_REQUIRED, 'Document root', 'web'),
                new InputOption('router', 'r', InputOption::VALUE_REQUIRED, 'Path to custom router script'),
            ))
            ->setName('server:run')
            ->setDescription('Runs PHP built-in web server')
            ->setHelp(<<<EOF
The <info>%command.name%</info> runs PHP built-in web server:

  <info>%command.full_name%</info>

To change default bind address and port use the <info>address</info> argument:

  <info>%command.full_name% 127.0.0.1:8080</info>

To change default docroot directory use the <info>--docroot</info> option:

  <info>%command.full_name% --docroot=htdocs/</info>

If you have custom docroot directory layout, you can specify your own
router script using <info>--router</info> option:

  <info>%command.full_name% --router=router.php</info>

See also: http://www.php.net/manual/en/features.commandline.webserver.php
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = $this->getConfig()->getEnv();

        if ('prod' === $env) {
            $output->writeln('<error>Running PHP built-in server in production environment is NOT recommended!</error>');
        }

        $router = $input->getOption('router') ?: $this->getDefaultRouter();
        ;

        $output->writeln(sprintf("Server running on <info>%s</info>\n", $input->getArgument('address')));

        $builder = new ProcessBuilder(array(PHP_BINARY, '-S', $input->getArgument('address'), $router));
        $builder->setWorkingDirectory($input->getOption('docroot'));
        $builder->setTimeout(null);
        $builder->getProcess()->run(function ($type, $buffer) use ($output) {
            if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                $output->write($buffer);
            }
        });
    }

    /**
     * @return string
     */
    protected function getDefaultRouter()
    {
        return  realpath(__DIR__ . "/../configs/router.php"); die;
    }
}
