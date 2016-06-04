<?php
namespace Thessia\Tasks;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateAPIDocs extends Command
{
    protected function configure()
    {
        $this
            ->setName("update:apidocs")
            ->setDescription("Update the API Documentation");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Updatedocs task");
    }
}