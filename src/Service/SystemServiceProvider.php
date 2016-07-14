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

namespace Thessia\Service;

use League\Container\ServiceProvider\AbstractServiceProvider;
use MongoDB\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Thessia\Lib\Cache;
use Thessia\Lib\Config;
use Thessia\Lib\cURL;
use Thessia\Lib\Db;
use Thessia\Lib\SessionHandler;
use Thessia\Lib\Timer;
use Thessia\Model\CCP\blueprints;
use Thessia\Model\CCP\categoryIDs;
use Thessia\Model\CCP\certificates;
use Thessia\Model\CCP\constellations;
use Thessia\Model\CCP\graphicIDs;
use Thessia\Model\CCP\groupIDs;
use Thessia\Model\CCP\iconIDs;
use Thessia\Model\CCP\landmarks;
use Thessia\Model\CCP\regions;
use Thessia\Model\CCP\skinLicenses;
use Thessia\Model\CCP\skinMaterials;
use Thessia\Model\CCP\skins;
use Thessia\Model\CCP\solarSystems;
use Thessia\Model\CCP\tournamentRuleSets;
use Thessia\Model\CCP\typeIDs;
use Thessia\Model\EVE\Alliances;
use Thessia\Model\EVE\Characters;
use Thessia\Model\EVE\Corporations;
use Thessia\Model\EVE\Crest;
use Thessia\Model\EVE\Killmails;
use Thessia\Model\EVE\Parser;
use Thessia\Model\EVE\Participants;
use Thessia\Model\EVE\Prices;

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
        "killmails",
        "blueprints",
        "categoryIDs",
        "certificates",
        "constellations",
        "graphicIDs",
        "groupIDs",
        "iconIDs",
        "landmarks",
        "regions",
        "skinLicenses",
        "skinMaterials",
        "skins",
        "solarSystems",
        "tournamentRuleSets",
        "typeIDs",
        "crest",
        "curl",
        "prices",
        "alliances",
        "corporations",
        "characters",
        "participants",
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
        $streamHandler = new StreamHandler(realpath(__DIR__ . "/../../logs/thessia.log"), Logger::INFO, true, 777,
            false);
        $log = new Logger("Thessia");
        $log->pushHandler($streamHandler);
        $container->share("log", $log);

        // Add the Session handler
        $container->share("session", SessionHandler::class)->withArgument("cache");

        // Add the Timer
        $container->share("timer", Timer::class);

        // Add the Cache
        $container->share("cache", Cache::class)->withArgument("config");

        // Add the CCP
        $container->share("db",
            Db::class)->withArgument("cache")->withArgument("log")->withArgument("timer")->withArgument("config")->withArgument("request");

        // Add MongoDB
        $mongo = new Client("mongodb://localhost:27017", array(),
            array("typeMap" => array("root" => "array", "document" => "array", "array" => "array")));
        $container->share("mongo", $mongo);

        // Add cURL
        $container->share("curl", cURL::class)->withArgument("cache");

        // Models
        $container->share("blueprints",
            blueprints::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("categoryIDs",
            categoryIDs::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("certificates",
            certificates::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("constellations",
            constellations::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("graphicIDs",
            graphicIDs::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("groupIDs",
            groupIDs::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("iconIDs",
            iconIDs::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("landmarks",
            landmarks::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("regions",
            regions::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("skinLicenses",
            skinLicenses::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("skinMaterials",
            skinMaterials::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("skins", skins::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("solarSystems",
            solarSystems::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("tournamentRuleSets",
            tournamentRuleSets::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("typeIDs",
            typeIDs::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");

        $container->share("crest", Crest::class);
        $container->share("prices",
            Prices::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("alliances",
            Alliances::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("corporations",
            Corporations::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("characters",
            Characters::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("participants",
            Participants::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("killmails",
            Killmails::class)->withArgument("config")->withArgument("mongo")->withArgument("cache");
        $container->share("parser", Parser::class)
            ->withArgument("typeIDs")->withArgument("solarSystems")->withArgument("prices")->withArgument("killmails")->withArgument("alliances")->withArgument("corporations")
            ->withArgument("characters")->withArgument("groupIDs")->withArgument("crest")->withArgument("curl")->withArgument("cache")->withArgument("mongo");

    }
}