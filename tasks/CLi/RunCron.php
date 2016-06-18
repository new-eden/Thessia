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

namespace Thessia\Tasks\CLi;

use Rena\Lib\Cache;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCron extends Command
{
    protected function configure()
    {
        $this
            ->setName("run:cron")
            ->setDescription("Run cronjobs");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Enable the garbage collector
        gc_enable();

        // Import the container, yes this is ugly
        global $container;

        $run = true;
        $cnt = 0;

        do {
            if ($cnt >= 5000) {
                $output->writeln("Collecting garbage..");
                gc_collect_cycles();
                $cnt = 0;
            }
            $cnt++;

            $files = glob(__DIR__ . "/Cron/*.php");
            foreach ($files as $file) {
                require_once($file);
                $baseName = basename($file);
                $className = str_replace(".php", "", $baseName);
                $import = "\\Rena\\Tasks\\Cron\\{$className}";
                $md5 = md5($baseName);
                $currentTime = time();

                /** @var Cache $cache */
                $cache = $container->get("cache");

                // Lets check if the current cron is running
                if ($cache->get($md5 . "_pid") !== false) {
                    $pid = $cache->get($md5 . "_pid");
                    $status = pcntl_waitpid($pid, $status, WNOHANG);
                    if ($status == -1) {
                        $cache->delete($md5 . "_pid");
                    } else {
                        usleep(500000);
                        continue;
                    }
                }

                $lastRan = $cache->get($md5) > 0 ? $cache->get($md5) : 0;
                $class = new $import();
                $interval = $class->getRunTimes();

                if ($interval == 0) {
                                    continue;
                }

                if ($currentTime > ($lastRan + $interval)) {
                    $date = date("Y-m-d H:i:s");
                    $output->writeln("{$date}: Running cronjob: {$className} (Interval: {$interval})");

                    try {
                        $pid = pcntl_fork();
                        if ($pid === 0) {
                            $cont = include(__DIR__ . "/../config/Dependencies.php");
                            $cache = $cont->get("cache");
                            $pid = getmypid();
                            $cache->set($md5 . "_pid", $pid);
                            $class->execute($cont);
                            exit();
                        }

                        $cache->set($md5, time());
                    } catch (\Exception $e) {
                        $output->writeln("Error: (pid: " . getmypid() . ")" . $e->getMessage());
                        //$run = false;
                        //posix_kill(getmypid(), 9);
                        //exit();
                    }
                }
            }
            usleep(500000);
        } while ($run == true);

    }
}