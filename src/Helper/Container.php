<?php

namespace Thessia\Helper;

use League\Container\Container;
use League\Container\ReflectionContainer;

if (! function_exists('getContainer')) {
    // Initialize and save the container instance
    function getContainer() {
        static $container;

        if(!isset($container)) {
            $container = new Container;

            //. Attempt to autowire class constructor dependencies
            $container->delegate(
                new ReflectionContainer
            );

            // Register the config file
            $container->add("configFile", __DIR__ . "/../../config/config.php");

            // Add the default system service provider
            $container->addServiceProvider(\Thessia\Service\SystemServiceProvider::class);
        }
        return $container;
    }
}