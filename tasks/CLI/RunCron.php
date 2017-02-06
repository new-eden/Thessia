<?php
// Declare ticks..
declare(ticks = 1);

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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCron extends Command
{
    private $cronjobs = array();
    private $runningJobs = array();

    protected function configure()
    {
        $this
            ->setName("run:cron")
            ->setDescription("Run cronjobs");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Time limits..
        set_time_limit(0);
        ini_set("max_execution_time", 0);

        // Enable garbage collection
        gc_enable();

        // Load the container
        $container = getContainer();
        $log = $container->get("log");
        $cache = $container->get("cache");

        // Populate runningJobs from Redis
        $this->runningJobs = unserialize($cache->get("runningCronJobs"));

        //var_dump($this->runningJobs); die();

        // Load all the cronjobs and populate the cronjobs array
        $files = glob(__DIR__ . "/../Cron/*.php");
        foreach ($files as $file) {
            require_once($file);
            $baseName = basename($file);
            $className = str_replace(".php", "", $baseName);
            $nameSpace = "\\Thessia\\Tasks\\Cron\\{$className}";
            $load = new $nameSpace();
            $runTime = $load->getRuntimes();
            $output->writeln("Adding {$nameSpace} to list of cronjobs, with an interval of {$runTime} seconds");

            $this->cronjobs[$nameSpace] = $runTime;
        }

        // Spit out some diagnostic info for the terminal..
        $cronjobCount = count($this->cronjobs);
        $output->writeln("Loaded {$cronjobCount} cronjobs...");

        // Startup the main loop
        $run = true;
        $loopCount = 0;
        do {
            if ($loopCount >= 500) {
                $output->writeln("Doing garbage collection...");
                gc_collect_cycles();
                $loopCount = 0;
            }

            // Increment the loopCount
            $loopCount++;

            foreach ($this->cronjobs as $className => $time) {
                // Check and make sure it's not in the runningJobs array
                if (isset($this->runningJobs[$className])) {
                    $token = $this->runningJobs[$className]["token"];
                    $lastRan = $this->runningJobs[$className]["lastRan"];

                    $timeSinceItRanLast = time() - $lastRan;
                    $status = new \Resque_Job_Status($token);
                    if ($timeSinceItRanLast >= $time && ($status->get() != \Resque_Job_Status::STATUS_WAITING || $status->get() != \Resque_Job_Status::STATUS_RUNNING)) {
                        unset($this->runningJobs[$className]);
                    }
                } else {
                    $date = date("Y-m-d H:i:s");
                    $log->addInfo("Scheduling cronjob: {$className} (Interval: {$time})");
                    $output->writeln("{$date}: Scheduling cronjob: {$className} (Interval: {$time})");
                    $token = \Resque::enqueue("cron", $className, null, true);
                    $this->runningJobs[$className] = array("token" => $token, "lastRan" => time());

                    // Insert the runningCronJobs array into Redis, in case we die..
                    $serialized = serialize($this->runningJobs);
                    $cache->set("runningCronJobs", $serialized);
                }

                usleep(500000);
            }

            // Sleep for a second between runs, so we don't go and eat 99.99% cpu
            sleep(1);
        } while ($run == true);
    }
}