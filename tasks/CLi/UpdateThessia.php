<?php
namespace Thessia\Tasks\CLi;

use JBZoo\PimpleDumper\PimpleDumper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateThessia extends Command
{
    protected function configure()
    {
        $this
            ->setName("update:thessia")
            ->setDescription("Updates composer and other stuff");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $container;
    }
}