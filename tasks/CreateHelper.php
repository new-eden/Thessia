<?php
namespace Thessia\Tasks;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class CreateHelper extends Command
{
    protected function configure()
    {
        $this
            ->setName("create:helper")
            ->setDescription("Create a helper");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = prompt("Name of Helper");

        $directory = __DIR__ . "/../src/Helpers/";

        $file = file_get_contents(__DIR__ . "/../Scaffolds/helper_template.txt");

        $file = str_replace("?name", ucfirst($name), $file);

        if (is_dir($directory) && !is_writable($directory)) {
            $output->writeln("The {$directory} directory is not writable");
            return;
        }

        if (!is_dir($directory)) {
            $helper = $this->getHelper("question");
            $question = new ConfirmationQuestion("Directory doesn't exist. Would you like to try and create it?", false);

            if(!$helper->ask($input, $output, $question))
                return;

            @mkdir($directory);
            if (!is_dir($directory)) {
                $output->writeln("<error>Couldn't create directory.</error>");
                return;
            }
        }
        if (!file_exists($directory . ucfirst($name) . ".php")) {
            $fh = fopen($directory . ucfirst($name) . ".php", "w");
            fwrite($fh, $file);
            fclose($fh);
            $className = ucfirst($name) . ".php";
            $output->writeln("Success, {$name} has been created");
        } else {
            $output->writeln("Error, {$name} already exists.");
        }
    }
}