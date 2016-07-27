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

use JBZoo\PimpleDumper\PimpleDumper;
use Jenssegers\Lean\SlimServiceProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thessia\Service\SystemServiceProvider;

class UpdateThessia extends Command
{
    protected function configure()
    {
        $this
            ->setName("update")
            ->setDescription("Updates composer and other stuff");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Generate the .phpstorm.meta.php file
        $this->phpStormMeta();

        // Update composer
    }

    private function phpStormMeta() {
        $container = getContainer();
        $container->addServiceProvider(new SlimServiceProvider());
        $systemProvider = new SystemServiceProvider();
        $slimProvider = new SlimServiceProvider();
        $systemProvides = $systemProvider->provides();
        $slimProvides = $slimProvider->provides();

        $provides = array_merge($systemProvides, $slimProvides);

        // Generate the instance array
        $instanceArray = array();
        foreach($provides as $service) {
            if(is_object($container->get($service))) {
                $instance = get_class($container->get($service));
                $instanceArray[$service] = $instance;
            }
        }

        // Manually added parts
        $instanceArray["render"] = '\Thessia\Lib\Render';

        // Generate the phpstorm meta file and output it to the root of the directory.
        $location = __DIR__ . "/../../.phpstorm.meta.php";

        $data = "<?php
namespace PHPSTORM_META {
    \$STATIC_METHOD_TYPES = [
        \\Interop\\Container\\ContainerInterface::get('') => [\n";

        //"mongo" instanceof Client,
        foreach($instanceArray as $key => $inst)
            $data .= "          '{$key}' instanceof {$inst},\n";

        // "" == "@",
        $data .= "
        ]
    ];
}";

        file_put_contents($location, $data);
    }
}