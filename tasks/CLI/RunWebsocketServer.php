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

use Ratchet\App;
use React\EventLoop\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Thessia\Tasks\WebSockets\EchoWebSocket;
use Thessia\Tasks\WebSockets\KillsWebSocket;

class RunWebSocketServer extends Command
{
    protected function configure()
    {
        $this
            ->setName("run:wsserver")
            ->setDescription("Run the websocket server")
            ->addOption("host", null, InputOption::VALUE_OPTIONAL, "WebSocket host. Default is 0.0.0.0", "0.0.0.0")
            ->addOption("port", null, InputOption::VALUE_OPTIONAL, "WebSocket port. Default is 474", 8800)
            ->addOption("httpHost", null, InputOption::VALUE_OPTIONAL, "WebSocket httpHost. Default is localhost", "localhost");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Load the container
        $container = getContainer();

        $output->writeln("Starting Websocket Instances");

        // Create the loop
        $loop = Factory::create();

        // Fire up Ratchet
        $ratchet = new App($input->getOption("httpHost"), $input->getOption("port"), $input->getOption("host"), $loop);

        // Load all endpoints
        foreach(glob(__DIR__ . "/../WebSockets/*.php") as $include)
            require_once($include);

        // Startup the endpoints.
        $echoWebSocket = new EchoWebSocket($container, $loop);
        $killsWebSocket = new KillsWebSocket($container, $loop);

        // Add routes
        $ratchet->route("/echo", $echoWebSocket, array("*"));
        $ratchet->route("/kills", $killsWebSocket, array("*"));

        // Start the loop
        $loop->run();
    }
}