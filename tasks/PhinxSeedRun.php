<?php

namespace Thessia\Tasks;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PhinxSeedRun extends \Phinx\Console\Command\SeedRun
{
    protected function configure()
    {
        parent::configure();

        $this->addOption('--environment', '-e', InputOption::VALUE_REQUIRED, 'The target environment');

        $this->setName('phinx:seedrun')
            ->setDescription('Run database seeders')
            ->addOption('--seed', '-s', InputOption::VALUE_REQUIRED, 'What is the name of the seeder?')
            ->setHelp(
                <<<EOT
                The <info>seed:run</info> command runs all available or individual seeders

<info>phinx seed:run -e development</info>
<info>phinx seed:run -e development -s UserSeeder</info>
<info>phinx seed:run -e development -v</info>

EOT
            );
    }
}
