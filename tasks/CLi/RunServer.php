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

use PHPPM\Commands\StartCommand;
use PHPPM\ProcessManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RunServer
 * @package Thessia\Tasks
 */
class RunServer extends StartCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName("run:server")
            ->setDescription("Starts the server");

        $this->configurePPMOptions($this);
    }

    protected function configurePPMOptions(Command $command)
    {
        $command
            ->addOption("bridge", null, InputOption::VALUE_OPTIONAL,
                "The bridge we use to convert a ReactPHP-Request to your target framework.",
                "Thessia\Middleware\Bridge")
            ->addOption("bootstrap", null, InputOption::VALUE_OPTIONAL,
                "The class that will be used to bootstrap your application", "Thessia\Middleware\Bootstrap")
            ->addOption("host", null, InputOption::VALUE_OPTIONAL, "Load-Balancer host. Default is 127.0.0.1",
                "127.0.0.1")
            ->addOption("port", null, InputOption::VALUE_OPTIONAL, "Load-Balancer port. Default is 31337", 3337)
            ->addOption("workers", null, InputOption::VALUE_OPTIONAL,
                "Worker count. Default is 8. Should be minimum equal to the number of CPU cores.", 8)
            ->addOption("app-env", null, InputOption::VALUE_OPTIONAL,
                "The environment that your application will use to bootstrap (if any)", "dev")
            ->addOption("debug", null, InputOption::VALUE_OPTIONAL,
                "Activates debugging so that your application is more verbose, enables also hot-code reloading. 1|0", 1)
            ->addOption("logging", null, InputOption::VALUE_OPTIONAL, "Deactivates the http logging to stdout. 1|0", 1)
            ->addOption("static", null, InputOption::VALUE_OPTIONAL, "Deactivates the static file serving. 1|0", 1)
            ->addOption("max-requests", null, InputOption::VALUE_OPTIONAL,
                "Max requests per worker until it will be restarted", 3000)
            ->addOption("concurrent-requests", null, InputOption::VALUE_OPTIONAL,
                "If a worker is allowed to handle more than one request at the same time. This can lead to issues when the application does not support it but makes it faster. (like when they operate on globals at the same time) 1|0",
                0)
            ->addOption("cgi-path", null, InputOption::VALUE_OPTIONAL, "Full path to the php-cgi executable", false)
            ->addOption("socket-path", null, InputOption::VALUE_OPTIONAL,
                "Path to a folder where socket files will be placed. Relative to working-directory or cwd()",
                "cache/ppm/");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($workingDir = $input->getArgument("working-directory")) {
            chdir($workingDir);
        }

        $config = $this->loadConfig($input, $output);

        if ($path = $this->getConfigPath()) {
            $modified = "";
            $fileConfig = json_decode(file_get_contents($path), true);
            if (json_encode($fileConfig) !== json_encode($config)) {
                $modified = ", modified by command arguments";
            }
            $output->writeln(sprintf("<info>Read configuration %s%s.</info>", $path, $modified));
        }
        $output->writeln(sprintf("<info>%s</info>", getcwd()));

        $this->renderConfig($output, $config);

        $handler = new ProcessManager($output, $config["port"], $config["host"], $config["workers"]);

        $handler->setBridge("\\Thessia\\Middleware\\Bridge");
        $handler->setAppBootstrap("\\Thessia\\Middleware\\Bootstrap");

        $handler->setAppEnv($config["app-env"]);
        $handler->setDebug((boolean)$config["debug"]);
        $handler->setLogging((boolean)$config["logging"]);
        $handler->setMaxRequests($config["max-requests"]);
        $handler->setPhpCgiExecutable($config["cgi-path"]);
        $handler->setSocketPath($config["socket-path"]);
        $handler->setConcurrentRequestsPerWorker($config["concurrent-requests"]);
        $handler->setServingStatic($config["static"]);

        $handler->run();
    }
}