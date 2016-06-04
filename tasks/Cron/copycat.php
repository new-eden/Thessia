<?php
namespace Thessia\Tasks\Cron;

use Slim\Container;

class copycat
{
    /**
     * @param Container $container
     */
    public static function execute(Container $container)
    {
        echo "I'm a copycat, mijau..\n";

        var_dump($container["db"]->query("SELECT 1"));
        var_dump($container["db"]->query("SELECT 2"));
    }

    /**
     * Defines how often the cronjob runs, every 1 second, every 60 seconds, every 86400 seconds, etc.
     */
    public static function getRunTimes()
    {
        return 1;
    }
}