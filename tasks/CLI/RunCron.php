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

use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thessia\Lib\Cache;

class RunCron extends Command
{
    private $child;

    protected function configure()
    {
        $this
            ->setName("run:cron")
            ->setDescription("Run cronjobs");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Loading the cronjob task...");
        // Enable the garbage collector
        gc_enable();

        // Import the container
        $container = getContainer();
        /** @var Logger $log */
        $log = $container->get("log");
        /** @var Cache $cache */
        $cache = $container->get("cache");

        // Load the cronjobs
        $cronjobs = glob(__DIR__ . "/../Cron/*.php");
        foreach($cronjobs as $file) {
            $baseName = str_replace(".php", "", basename($file));
            $output->writeln("Loading: {$baseName}...");
            require_once($file);
        }

        $output->writeln("Done loading cronjobs...");

        // Setup the default variables for running and garbage collecting
        $run = true;
        $cnt = 0;

        do {
            // If we've run through 5000 loops, we'll collect some garbage..
            if ($cnt >= 5000) {
                $output->writeln("Collecting garbage..");
                gc_collect_cycles();
                $cnt = 0;
            }

            // Increment the loop
            $cnt++;

            // Iterate over all the cronjobs, and launch them as needed
            foreach($cronjobs as $file) {
                $status = 0;
                $baseName = basename($file);
                $className = str_replace(".php", "", $baseName);
                $date = date("Y-m-d H:i:s");
                $import = "\\Thessia\\Tasks\\Cron\\{$className}";
                $md5 = md5($baseName);
                $currentTime = time();

                // Check if the current cron is running
                if($cache->exists($md5 . "_pid")) {
                    $pid = $cache->get($md5 . "_pid");
                    $status = pcntl_waitpid($pid, $status, WNOHANG);

                    if($status == -1)
                        $cache->delete($md5 . "_pid");
                    else
                        continue;
                }

                // Figure out when it last ran
                $lastRan = $cache->exists($md5) ? $cache->get($md5) : 0;

                // Load the cronjob
                $class = new $import();
                $interval = $class->getRuntimes();

                // If the interval is 0, we'll just skip it
                if($interval == 0)
                    continue;

                // If it has been the required amount of time since it last ran, we'll run it again!
                if($currentTime > ($lastRan + $interval)) {
                    $log->addInfo("Running cronjob: {$className} (Interval: {$interval})");
                    $output->writeln("{$date}: Running cronjob: {$className} (Interval: {$interval})");

                    try {
                        if(($pid = pcntl_fork()) == 0) {
                            // Load the container in the child
                            $container = getContainer();
                            // Load the cache in the child
                            $cache = $container->get("cache");
                            $pid = getmypid();
                            // Insert the childs pid to the cache
                            $cache->set($md5 . "_pid", $pid);
                            // Execute the execute function in the child
                            $class->execute($container);
                            // Set when the child was run
                            $cache->set($md5, time());
                            // Exit the child
                            exit();
                        }
                    } catch (\Exception $e) {
                        $myPid = getmypid();
                        $output->writeln("Error with pid: {$myPid}, exiting because of: {$e->getMessage()}");
                        $run = false;
                        posix_kill($myPid, 9);
                        exit();
                    }
                }
            }

            // Sleep for a second between each loop
            sleep(1);
        } while ($run == true);
    }
}