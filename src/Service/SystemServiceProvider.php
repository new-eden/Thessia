<?php

namespace Thessia\Service;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Thessia\Lib\Cache;
use Thessia\Lib\Config;
use Thessia\Lib\Db;
use Thessia\Lib\Render;
use Thessia\Lib\SessionHandler;
use Thessia\Lib\Timer;

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
        // Add the config
        $this->getContainer()->share("config", Config::class)->withArgument("configFile")->withArgument("log");

        // Add the logger
        $this->getContainer()->share("log", function() use ($container){
            $logger = new Logger($container->get("config")->get("name", "settings", "Thessia"));
            $logger->pushHandler(new StreamHandler($container->get("config")->get("path", "settings", __DIR__ . "/../../logs/thessia.log"), Logger::WARNING));

            return $logger;
        });

        // Add the twig view
        $this->getContainer()->share("view", function() use ($container) {
            $twig = new Twig(__DIR__ . "/../../templates", $container->get("config")->getAll("settings")["view"]);
            $twig->addExtension(new TwigExtension($container->get("router"), $container->get("request")->getUri()));
            $twig->addExtension(new \Twig_Extension_Debug());

            return $twig;
        });

        // Add the Cache
        $this->getContainer()->share("cache", Cache::class)->withArgument("config");

        // Add the Database
        $this->getContainer()->share("db", Db::class)->withArgument("cache")->withArgument("log")->withArgument("timer")->withArgument("config")->withArgument("request");

        // Add the Renderer
        $this->getContainer()->share("render", Render::class)->withArgument("view");

        // Add the Session handler
        $this->getContainer()->share("session", SessionHandler::class)->withArgument("cache");

        // Add the Timer
        $this->getContainer()->share("timer", Timer::class);
    }
}