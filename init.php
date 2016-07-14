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

use League\Container\Container;
use League\Container\ReflectionContainer;

// change to the main directory
chdir(__DIR__);

// Load the autoloader
if (file_exists(__DIR__ . "/vendor/autoload.php")) {
    /** @var Composer\Autoload\ClassLoader $loader */
    $loader = require_once __DIR__ . "/vendor/autoload.php";
} else {
    throw new Exception("vendor/autoload.php not found, make sure you run composer install");
}

// Initialize the container
$container = getContainer();

// Global function(s)
// Dump and die!
function dd($input) {
    var_dump($input); die();
}

function getContainer() {
    static $container;

    if (!isset($container)) {
        $container = new Container();

        // Autowire class constructor dependencies
        $container->delegate(new ReflectionContainer());

        // Register default config file
        if (file_exists(__DIR__ . "/config/config.php")) {
                    $container->add("configFile", __DIR__ . "/config/config.php");
        } else {
                    throw new \Exception("Error, config.php missing in the config directory");
        }

        // Load the dependencies
        $container->addServiceProvider(new \Thessia\Service\SystemServiceProvider);
    }

    return $container;
}