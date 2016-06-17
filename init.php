<?php

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

    if(!isset($container)) {
        $container = new Container();

        // Autowire class constructor dependencies
        $container->delegate(new ReflectionContainer());

        // Register default config file
        if(file_exists(__DIR__ . "/config/config.php"))
            $container->add("configFile", __DIR__ . "/config/config.php");
        else
            throw new \Exception("Error, config.php missing in the config directory");

        // Load the dependencies
        $container->addServiceProvider(new \Thessia\Service\SystemServiceProvider);
    }

    return $container;
}