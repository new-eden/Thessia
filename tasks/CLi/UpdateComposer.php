<?php
namespace Thessia\Tasks\CLi;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateComposer extends Command
{
    protected function configure()
    {
        $this
            ->setName("update:composer")
            ->setDescription("Update Composer and the Autoloader");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("update composer task");
    }
}