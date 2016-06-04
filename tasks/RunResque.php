<?php
namespace Thessia\Tasks;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunResque extends Command
{
    protected function configure()
    {
        $this
            ->setName("run:resque")
            ->setDescription("Run the Resque jobs");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        require_once(__DIR__ . "/../vendor/chrisboulton/php-resque/lib/Resque.php");
        require_once(__DIR__ . "/../vendor/chrisboulton/php-resque/lib/Resque/Worker.php");
        require_once(__DIR__ . "/../Config/Dependencies.php");

        $queues = "rt,high,med,low";
        $logLevel = \Resque_Worker::LOG_NORMAL;
        $interval = 5;
        $worker = new \Resque_Worker($queues);
        $worker->logLevel = $logLevel;
        $output->writeln("Starting Resque Worker");
        $worker->work($interval);
    }
}