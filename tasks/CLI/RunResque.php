<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2016. Michael Karbowiak
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Thessia\Tasks\CLI;

use React\EventLoop\Factory;
use React\Stomp\Factory as StompFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Thessia\Lib\Config;

class RunResque extends Command
{
    protected function configure()
    {
        $this
            ->setName("run:resque")
            ->setDescription("Run the Resque jobs")
            ->addOption("queue", null, InputOption::VALUE_REQUIRED, "The queue name for which it should be listening to..", array("rt", "high", "med", "low"));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Require the resque files.. because it doesn't work any other way apparently..
        require_once(__DIR__ . "/../../vendor/chrisboulton/php-resque/lib/Resque.php");
        require_once(__DIR__ . "/../../vendor/chrisboulton/php-resque/lib/Resque/Worker.php");

        // Load all the resque files
        foreach (glob(__DIR__ . "/../Resque/*.php") as $file) {
            require_once($file);
        }

        if(is_array($input->getOption("queue")))
            $queues = $input->getOption("queue");
        else
            $queues = array($input->getOption("queue"));

        $logLevel = \Resque_Worker::LOG_NORMAL;
        $interval = 1;
        $worker = new \Resque_Worker($queues);
        $worker->logLevel = $logLevel;
        $output->writeln("Starting Resque Worker");
        $worker->work($interval);
    }
}