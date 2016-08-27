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
use React\ZMQ\Context;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ZMQ;

class RunZMQ extends Command
{
    protected function configure()
    {
        $this
            ->setName("run:zmq")
            ->setDescription("Run the ZMQ -> Stomp relay")
            ->addOption("host", null, InputOption::VALUE_OPTIONAL, "WebSocket host. Default is 127.0.0.1", "127.0.0.1")
            ->addOption("port", null, InputOption::VALUE_OPTIONAL, "WebSocket port. Default is 474", 5555);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Load the container
        $container = getContainer();
        $config = $container->get("config");

        $output->writeln("Starting ZMQ and Stomp");

        $port = $input->getOption("port");
        $host = $input->getOption("host");

        // Create the loop
        $loop = Factory::create();

        // Start ZMQ
        $context = new Context($loop);
        $pull = $context->getSocket(ZMQ::SOCKET_PULL, "killmail");
        $pull->bind("tcp://{$host}:{$port}");

        // Start Stomp (Requires the php extension for stomp)
        $stomp = new \Stomp("127.0.0.1:61613", $config->get("username", "stomp"), $config->get("password", "stomp"));

        // On a message in ZMQ, we push it out over Stomp
        $pull->on("message", function($killmail) use ($stomp) {
            $stomp->send("/topic/kills", $killmail);
        });

        $loop->run();
    }
}