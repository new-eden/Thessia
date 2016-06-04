<?php
namespace Thessia\Tasks;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PhinxTest extends \Phinx\Console\Command\Test
{
    protected function configure()
    {
        parent::configure();

        $this->addOption('--environment', '-e', InputOption::VALUE_REQUIRED, 'The target environment');

        $this->setName('phinx:test')
            ->setDescription('Verify the configuration file')
            ->setHelp(
                <<<EOT
                The <info>test</info> command verifies the YAML configuration file and optionally an environment

<info>phinx test</info>
<info>phinx test -e development</info>

EOT
            );
    }
}
