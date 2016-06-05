<?php
namespace Thessia\Tasks\CLi;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class CreateWebsocket extends Command
{
    protected function configure()
    {
        $this
            ->setName("create:websocket")
            ->setDescription("Create a websocket endpoint");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = prompt("Name of Websocket");

        $directory = __DIR__ . "/WebSockets/";

        $file = file_get_contents(__DIR__ . "/../scaffolds/websocket_template.txt");

        $file = str_replace("?name", ucfirst($name), $file);

        if (is_dir($directory) && !is_writable($directory)) {
            $output->writeln("The {$directory} directory is not writable");
            return;
        }

        if (!is_dir($directory)) {
            $helper = $this->getHelper("question");
            $question = new ConfirmationQuestion("Directory doesn't exist. Would you like to try and create it?", false);

            if (!$helper->ask($input, $output, $question))
                return;

            @mkdir($directory);
            if (!is_dir($directory)) {
                $output->writeln("<error>Couldn't create directory.</error>");
                return;
            }
        }
        if (!file_exists($directory . ucfirst($name) . "WebSocket.php")) {
            $fh = fopen($directory . ucfirst($name) . "WebSocket.php", "w");
            fwrite($fh, $file);
            fclose($fh);
            $className = ucfirst($name) . "WebSocket.php";
            $output->writeln("Success, WebSocket {$name} has been created");
        } else {
            $output->writeln("Error, WebSocket {$name} already exists.");
        }
    }
}