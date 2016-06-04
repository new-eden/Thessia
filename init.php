<?php

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
$container = new League\Container\Container;

// Load the system provider
$container->addServiceProvider(new \Jenssegers\Lean\SlimServiceProvider);

// Global function(s)
// Dump and die!
function dd($input) {
    var_dump($input); die();
}