<?php

namespace Thessia\Tasks\Phinx;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Init extends \Phinx\Console\Command\Init
{
    protected function configure()
    {
        $this->setName('phinx:init')
            ->setDescription('Initialize the application for Phinx')
            ->addArgument('path', InputArgument::OPTIONAL, 'Which path should we initialize for Phinx?')
            ->setHelp(sprintf(
                '%sInitializes the application for Phinx%s',
                PHP_EOL,
                PHP_EOL
            ));
    }
}
