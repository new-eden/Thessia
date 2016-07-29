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
use React\Stomp\Client;
use React\Stomp\Factory as StompFactory;
use React\Stomp\Protocol\Frame;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thessia\Lib\Config;

class RunStomp extends Command {
    protected function configure()
    {
        $this
            ->setName("run:stomp")
            ->setDescription("Run the Stomp fetcher process.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = getContainer();
        $config = $container->get("config");
        $log = $container->get("log");
        $mongo = $container->get("mongo");
        $collection = $mongo->selectCollection("thessia", "killmails");

        $loop = Factory::create();
        $factory = new StompFactory($loop);
        $clientArray = array("vhost" => "/", "login" => $config->get("username", "stomp"), "passcode" => $config->get("password", "stomp"));
        $client = $factory->createClient($clientArray);
        $client->connect()
            ->then(function(Client $client) use ($loop, $collection, $log, $config) {
                $client->subscribe("/topic/kills", function(Frame $frame) use ($collection, $log) {
                    if($this->isJson($frame->body)) {
                        // Unpack the json to an array
                        $data = json_decode($frame->body, true);

                        // Map the killID and killHash to variables
                        $killID = $data["killID"] ?? null;
                        $killHash = $data["crestHash"] ?? null;

                        // If killID and killHash are set, we'll see if a kill already exists, if one does not, we'll throw it after the parser
                        if($killID && $killHash) {
                            $exists = $collection->findOne(array("killID" => $killID));
                            if(empty($exists)) {
                                // Logging
                                $log->info("Got killmail from Stomp");

                                // Enqueue the mail for processing
                                \Resque::enqueue("rt", '\Thessia\Tasks\Resque\KillmailParser',
                                    array("killID" => $killID, "killHash" => $killHash));
                            }
                        }

                        // Unset $data again for the next loop
                        $data = null;
                    }
                });
            });

        $loop->run();
    }

    /**
     * Returns true if json, false if not.
     *
     * @param string $json
     * @return bool
     */
    private function isJson(string $json): bool {
        json_decode($json);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}