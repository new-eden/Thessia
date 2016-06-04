<?php

namespace Thessia\Tasks\Phinx;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Status extends \Phinx\Console\Command\Status
{
    protected function configure()
    {
        parent::configure();

        $this->addOption('--environment', '-e', InputOption::VALUE_REQUIRED, 'The target environment.');

        $this->setName('phinx:status')
            ->setDescription('Show migration status')
            ->addOption('--format', '-f', InputOption::VALUE_REQUIRED, 'The output format: text or json. Defaults to text.')
            ->setHelp(
                <<<EOT
                The <info>status</info> command prints a list of all Migrations, along with their current status

<info>phinx status -e development</info>
<info>phinx status -e development -f json</info>
EOT
            );
    }
}
