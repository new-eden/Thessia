<?php

namespace Thessia\Service;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

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
        "config",
        "cache",
        "db",
        "render",
        "session",
        "timer",
        "view"
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
        $container->share("log", "Monolog\\Logger")->withArgument($container->get("config")->get("name", "settings", "Thessia"));
        $container->get("log")->pushHandler(new StreamHandler($container->get("config")->get("path", "settings", __DIR__ . "/../../logs/thessia.log"), Logger::WARNING));

        // Add the config
        $container->share("config", "Thessia\\Lib\\Config")->withArgument("configFile")->withArgument("log");

        // Add the twig view
        $twig = new Twig(__DIR__ . "/../../templates", $container->get("config")->getAll("settings")["view"]);
        $twig->addExtension(new TwigExtension($container->get("router"), $container->get("request")->getUri()));
        $twig->addExtension(new \Twig_Extension_Debug());
        $container->share("view", $twig);


        // Add the Cache
        $container->share("cache", "\\Thessia\\Lib\\Cache")->withArgument("config");

        // Add the Database
        $container->share("db", "\\Thessia\\Lib\\Db")->withArgument("cache")->withArgument("log")->withArgument("timer")->withArgument("config")->withArgument("request");

        // Add the Renderer
        $container->share("render", "\\Thessia\\Lib\\Render")->withArgument("view");

        // Add the Session handler
        $container->share("session", "\\Thessia\\Lib\\Session")->withArgument("cache");

        // Add the Timer
        $container->share("timer", "\\Thessia\\Lib\\Timer");
    }
}