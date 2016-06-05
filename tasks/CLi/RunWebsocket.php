<?php
namespace Thessia\Tasks\CLi;

use Ratchet\App;
use React\EventLoop\Factory;
use React\ZMQ\Context;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunWebSocket extends Command
{
    protected function configure()
    {
        $this
            ->setName("run:websocket")
            ->setDescription("Run the websocket server")
            ->addOption("host", null, InputOption::VALUE_OPTIONAL, "WebSocket host. Default is 0.0.0.0", "0.0.0.0")
            ->addOption("port", null, InputOption::VALUE_OPTIONAL, "WebSocket port. Default is 474", 474)
            ->addOption("httpHost", null, InputOption::VALUE_OPTIONAL, "WebSocket httpHost. Default is localhost", "localhost");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $container;
        $output->writeln("Starting Websocket Instances");

        // Fire up Ratchet
        $ratchet = new App($input->getOption("httpHost"), $input->getOption("port"), $input->getOption("host"));

        // Find all websocket endpoints
        $endpoints = array(__DIR__ . "/WebSockets/*.php", __DIR__ . "/../App/WebSockets/*.php");

        // Load all endpoints
        foreach ($endpoints as $dir) {
            $files = glob($dir);
            foreach ($files as $file) {
                require_once($file);
                $baseName = basename($file);
                $className = str_replace(".php", "", $baseName);
                $urlPath = strtolower(str_replace("WebSocket", "", str_replace(".php", "", $baseName)));
                if (stristr($file, "App/")) {
                    $className = "\\App\\WebSockets\\{$className}";
                } else {
                    $className = "\\Rena\\Tasks\\WebSockets\\{$className}";
                }

                $output->writeln("Adding path: /{$urlPath} to WebSocket Instance");
                $ratchet->route("/{$urlPath}", new $className($container), array("*"));
            }
        }

        $ratchet->run();
    }
}