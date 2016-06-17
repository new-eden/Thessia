<?php

namespace Thessia\Service;

use League\Container\ServiceProvider\AbstractServiceProvider;
use MongoDB\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Thessia\Lib\Cache;
use Thessia\Lib\Config;
use Thessia\Lib\Db;
use Thessia\Lib\SessionHandler;
use Thessia\Lib\Timer;
use Thessia\Model\Database\killmails;

class SystemServiceProvider extends AbstractServiceProvider
{
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
        "config",
        "settings",
        "log",
        "view",
        "render",
        "timer",
        "cache",
        "db",
        "session",
        "mongo",
        "killmails"
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

        // Add the config
        $container->share("config", Config::class)->withArgument("configFile");

        // Add slim/twig/logger settings
        $settings = $container->get("config")->getAll("settings");
        $container->share("settings", $settings);

        // Add the logger
        $container->share("log", Logger::class)->withArgument($container->get("config")->get("name", "settings", "Thessia"));
        $container->get("log")->pushHandler(new StreamHandler($container->get("config")->get("path", "settings", __DIR__ . "/../../logs/thessia.log"), Logger::WARNING));

        // Add the Session handler
        $container->share("session", SessionHandler::class)->withArgument("cache");

        // Add the Timer
        $container->share("timer", Timer::class);

        // Add the Cache
        $container->share("cache", Cache::class)->withArgument("config");

        // Add the Database
        $container->share("db", Db::class)->withArgument("cache")->withArgument("log")->withArgument("timer")->withArgument("config")->withArgument("request");


        // Add MongoDB
        $container->share("mongo", Client::class);

        // Models
        //$container->share("killmails", killmails::class)->withArgument("config")->withArgument("mongo");
    }
}