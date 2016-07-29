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

use RedisQ\Action;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunRedisQ extends Command {
    protected function configure()
    {
        $this
            ->setName("run:redisq")
            ->setDescription("Run RedisQ fetcher process");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = getContainer();
        $log = $container->get("log");
        $mongo = $container->get("mongo");
        $collection = $mongo->selectCollection("thessia", "killmails");

        $run = true;
        do {
            try {
                $p = Action::listen("https://redisq.zkillboard.com/");

                if (!empty($p)) {
                    $killID = $p["killID"];
                    $killHash = $p["zkb"]["hash"];
                    $warID = $p["zkb"]["killmail"]["war"]["id"] ?? 0;

                    // If a killmail already exists, we'll not bother to insert it
                    $exists = $collection->findOne(array("killID" => $killID));
                    if (!empty($exists)) {
                        continue;
                    }

                    if ($killID && $killHash) {
                        // Logging
                        $log->info("Got killmail from RedisQ");

                        // Enqueue the mail for processing
                        \Resque::enqueue("rt", '\Thessia\Tasks\Resque\KillmailParser',
                            array("killID" => $killID, "killHash" => $killHash, "warID" => $warID));
                    }
                }
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        } while($run == true);
    }
}