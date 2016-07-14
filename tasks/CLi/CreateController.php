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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class CreateController extends Command
{
    protected function configure()
    {
        $this
            ->setName("create:controller")
            ->setDescription("Creates a controller");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = prompt("Name of Controller");

        $directory = __DIR__ . "/../src/Controllers/";

        $file = file_get_contents(__DIR__ . "/../scaffolds/controller_template.txt");

        $file = str_replace("?name", ucfirst($name), $file);

        if (is_dir($directory) && !is_writable($directory)) {
            $output->writeln("The {$directory} directory is not writable");
            return;
        }

        if (!is_dir($directory)) {
            $helper = $this->getHelper("question");
            $question = new ConfirmationQuestion("Directory doesn't exist. Would you like to try and create it?",
                false);

            if (!$helper->ask($input, $output, $question)) {
                return;
            }

            @mkdir($directory);
            if (!is_dir($directory)) {
                $output->writeln("<error>Couldn't create directory.</error>");
                return;
            }
        }
        if (!file_exists($directory . ucfirst($name) . "Controller.php")) {
            $fh = fopen($directory . ucfirst($name) . "Controller.php", "w");
            fwrite($fh, $file);
            fclose($fh);
            $className = ucfirst($name) . "Controller.php";
            $output->writeln("Success, Controller {$name} has been created");
        } else {
            $output->writeln("Error, Controller {$name} already exists.");
        }
    }
}