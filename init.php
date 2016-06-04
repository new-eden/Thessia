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
$container = new Container;

// Attempt to autowire class constructor dependencies
$container->delegate(new ReflectionContainer);

// Register the config file
$container->add("configFile", __DIR__ . "/config/config.php");

// Load the dependencies
$container->addServiceProvider(new \Thessia\Service\SystemServiceProvider);

// Load the slim dependency
$container->addServiceProvider(new \Jenssegers\Lean\SlimServiceProvider);

// Global function(s)
// Dump and die!
function dd($input) {
    var_dump($input); die();
}