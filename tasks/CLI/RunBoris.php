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

declare(ticks = 1);

namespace Thessia\Tasks\CLI;

use Boris\Boris;
use Boris\ExportInspector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunBoris extends Command
{
    protected function configure()
    {
        $this
            ->setName("run:boris")
            ->setDescription("Run the Boris REPL");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $boris = new Boris("Thessia>\n");
        $boris->setInspector(new ExportInspector());
        $boris->onStart('$container = getContainer();');
        $boris->onStart('echo "Welcome to Boris.\n The container from Thessia is available under \$container.\n";');
        $boris->onStart('echo "To use, say mongodb, simply do: \$mongo = \$container->get(\"mongo\");\n\n";');
        $boris->start();
    }
}