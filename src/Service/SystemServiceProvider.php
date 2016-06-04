<?php

namespace Thessia\Service;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class SystemServiceProvider extends AbstractServiceProvider {
    /**
     * The provides array is a way to let the container
     * know that a service is provided by this service
     * provider. Every service that is registered via
     * this service provider must have an alias added
     * to this array or it will be ignored.
     *
     * @var array
     */
    protected $provides = [
        "log",
        "config"
    ];

    /**
     * This is where the magic happens, within the method you can
     * access the container and register or retrieve anything
     * that you need to, but remember, every alias registered
     * within this method must be declared in the `$provides` array.
     */
    public function register()
    {
        $container = $this->getContainer();

        // Add the logger
        $container->share("log", "Monolog\\Logger")->withArgument("Thessia");
        $container->get("log")->pushHandler(new StreamHandler(__DIR__ . "/../../logs/thessia.log", Logger::WARNING));

        // Add the config
        $container->share("config", "Thessia\\Lib\\Config")->withArgument("configFile")->withArgument("log");

        // Add the twig view
        $container->share("view", new \Slim\Views\Twig(__DIR__ . "/../../templates", $container->get("config")->getAll("twig")));
        $container->get("view")->addExtension(new \Slim\Views\TwigExtension($container->get("router"), $container->get("request")->getUri()));
        $container->get("view")->addExtension(new \Twig_Extension_Debug());

        // Add the Cache
        // Add the Database
        // Add the Renderer
        // Add the Session handler
        // Add the Timer
    }
}