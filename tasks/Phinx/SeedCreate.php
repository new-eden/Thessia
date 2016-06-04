<?php

namespace Thessia\Tasks\Phinx;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class SeedCreate extends \Phinx\Console\Command\SeedCreate
{
    protected function configure()
    {
        parent::configure();

        $this->setName('phinx:seedcreate')
            ->setDescription('Create a new database seeder')
            ->addArgument('seedname', InputArgument::REQUIRED, 'What is the name of the seeder?')
            ->setHelp(sprintf(
                '%sCreates a new database seeder%s',
                PHP_EOL,
                PHP_EOL
            ));
    }
}
