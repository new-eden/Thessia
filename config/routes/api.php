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

$app->group("/api", function () use ($app) {
    $app->group("/character", function() use ($app) {
        $controller = new \Thessia\Controller\API\CharacterAPIController($app);
        $app->get("/count/", $controller("characterCount"));
        $app->get("/information/{characterID:[0-9]+}/", $controller("characterInformation"));
        $app->get("/find/{searchTerm:[A-Za-z0-9]+}/", $controller("findCharacter"));

        $app->group("/top", function() use ($app, $controller) {
            $app->get("/characters/{characterID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topCharacters"));
            $app->get("/corporations/{characterID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topCorporations"));
            $app->get("/alliances/{characterID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topAlliances"));
            $app->get("/ships/{characterID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topShips"));
            $app->get("/systems/{characterID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topSystems"));
            $app->get("/regions/{characterID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topRegions"));
        });
    });

    $app->group("/corporation", function() use ($app) {
        $controller = new \Thessia\Controller\API\CorporationAPIController($app);
        $app->get("/count/", $controller("corporationCount"));
        $app->get("/information/{corporationID:[0-9]+}/", $controller("corporationInformation"));
        $app->get("/find/{searchTerm:[^\\/]+}/", $controller("findCorporation"));
        $app->get("/members/{corporationID:[0-9]+}/", $controller("corporationMembers"));

        $app->group("/top", function() use ($app, $controller) {
            $app->get("/characters/{corporationID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topCharacters"));
            $app->get("/corporations/{corporationID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topCorporations"));
            $app->get("/alliances/{corporationID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topAlliances"));
            $app->get("/ships/{corporationID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topShips"));
            $app->get("/systems/{corporationID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topSystems"));
            $app->get("/regions/{corporationID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topRegions"));
        });
    });

    $app->group("/alliance", function() use ($app) {
        $controller = new \Thessia\Controller\API\AllianceAPIController($app);
        $app->get("/count/", $controller("allianceCount"));
        $app->get("/information/{allianceID:[0-9]+}/", $controller("allianceInformation"));
        $app->get("/find/{searchTerm:[^\\/]+}/", $controller("findAlliance"));
        $app->get("/members/{allianceID:[0-9]+}/", $controller("allianceMembers"));

        $app->group("/top", function() use ($app, $controller) {
            $app->get("/characters/{allianceID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topCharacters"));
            $app->get("/corporations/{allianceID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topAlliances"));
            $app->get("/alliances/{allianceID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topAlliances"));
            $app->get("/ships/{allianceID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topShips"));
            $app->get("/systems/{allianceID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topSystems"));
            $app->get("/regions/{allianceID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topRegions"));
        });
    });

    $app->group("/item", function() use ($app) {
        $controller = new \Thessia\Controller\API\ItemAPIController($app);
        $app->get("/count/", $controller("itemCount"));
        $app->get("/information/{typeID:[0-9]+}/", $controller("itemInformation"));
        $app->get("/find/{searchTerm:[^\\/]+}/", $controller("findItem"));

        $app->group("/top", function() use ($app, $controller) {
            $app->get("/characters/{itemID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topCharacters"));
            $app->get("/corporations/{itemID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topCorporations"));
            $app->get("/alliances/{itemID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topAlliances"));
            $app->get("/ships/{itemID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topShips"));
            $app->get("/systems/{itemID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topSystems"));
            $app->get("/regions/{itemID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topRegions"));
        });
    });

    /*

    $app->group("/item", function() use ($app) {
        $app->get("/information/:itemID/", function(int $itemID) {

        });

        $app->get("/find/:searchTerm/", function($searchTerm) use ($app, $controller) {

        });

        $app->group("/top", function() use ($app, $controller) {
            $app->get("/characters/:characterID(/:limit)/", function(int $characterID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/corporation/:corporationID(/:limit)/", function(int $corporationID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/alliances/:allianceID(/:limit)/", function(int $allianceID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/ships/:shipTypeID(/:limit)/", function(int $shipTypeID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/systems/:solarSystemID(/:limit)/", function(int $solarSystemID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/regions/:regionID(/:limit)/", function(int $regionID, int $limit = 10) use ($app, $controller) {

            });
        });
    });

    $app->group("/system", function() use ($app) {
        $app->get("/information/:solarSystemID/", function(int $solarSystemID) {

        });

        $app->get("/find/:searchTerm/", function($searchTerm) use ($app, $controller) {

        });

        $app->group("/top", function() use ($app, $controller) {
            $app->get("/characters/:characterID(/:limit)/", function(int $characterID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/corporation/:corporationID(/:limit)/", function(int $corporationID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/alliances/:allianceID(/:limit)/", function(int $allianceID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/ships/:shipTypeID(/:limit)/", function(int $shipTypeID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/systems/:solarSystemID(/:limit)/", function(int $solarSystemID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/regions/:regionID(/:limit)/", function(int $regionID, int $limit = 10) use ($app, $controller) {

            });
        });
    });

    $app->group("/region", function() use ($app) {
        $app->get("/information/:regionID/", function(int $regionID) {

        });

        $app->get("/find/:searchTerm/", function($searchTerm) use ($app, $controller) {

        });

        $app->group("/top", function() use ($app, $controller) {
            $app->get("/characters/:characterID(/:limit)/", function(int $characterID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/corporation/:corporationID(/:limit)/", function(int $corporationID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/alliances/:allianceID(/:limit)/", function(int $allianceID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/ships/:shipTypeID(/:limit)/", function(int $shipTypeID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/systems/:solarSystemID(/:limit)/", function(int $solarSystemID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/regions/:regionID(/:limit)/", function(int $regionID, int $limit = 10) use ($app, $controller) {

            });
        });
    });

    $app->group("/constellation", function() use ($app) {
        $app->get("/information/:constellationID/", function(int $constellationID) {

        });

        $app->get("/find/:searchTerm/", function($searchTerm) use ($app, $controller) {

        });

        $app->group("/top", function() use ($app, $controller) {
            $app->get("/characters/:characterID(/:limit)/", function(int $characterID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/corporation/:corporationID(/:limit)/", function(int $corporationID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/alliances/:allianceID(/:limit)/", function(int $allianceID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/ships/:shipTypeID(/:limit)/", function(int $shipTypeID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/systems/:solarSystemID(/:limit)/", function(int $solarSystemID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/regions/:regionID(/:limit)/", function(int $regionID, int $limit = 10) use ($app, $controller) {

            });
        });
    });

    $app->group("/celestial", function() use ($app) {
        $app->get("/information/:celestialID/", function(int $celestialID) {

        });

        $app->get("/find/:searchTerm/", function($searchTerm) use ($app, $controller) {

        });

        $app->group("/top", function() use ($app, $controller) {
            $app->get("/characters/:characterID(/:limit)/", function(int $characterID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/corporation/:corporationID(/:limit)/", function(int $corporationID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/alliances/:allianceID(/:limit)/", function(int $allianceID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/ships/:shipTypeID(/:limit)/", function(int $shipTypeID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/systems/:solarSystemID(/:limit)/", function(int $solarSystemID, int $limit = 10) use ($app, $controller) {

            });
            $app->get("/regions/:regionID(/:limit)/", function(int $regionID, int $limit = 10) use ($app, $controller) {

            });
        });
    });

    $app->group("/kill", function() use ($app) {
        $app->post("/add/", function() use ($app, $controller) {

        });

        $app->get("/count/", function() use ($app, $controller) {

        });

        $app->get("/mail/:killID/", function(int $killID) use ($app, $controller) {

        });
    });

    $app->group("/killlist", function() use ($app) {
        $app->get("/latest/", function() use ($app, $controller) {

        });

        $app->get("/bigkills/", function() use ($app, $controller) {

        });

        $app->get("/wspace/", function() use ($app, $controller) {

        });

        $app->get("/highsec/", function() use ($app, $controller) {

        });

        $app->get("/lowsec/", function() use ($app, $controller) {

        });

        $app->get("/nullsec/", function() use ($app, $controller) {

        });

        $app->get("/solo/", function() use ($app, $controller) {

        });

        $app->get("/5b/", function() use ($app, $controller) {

        });

        $app->get("/10b/", function() use ($app, $controller) {

        });

        $app->get("/capitals/", function() use ($app, $controller) {

        });

        $app->get("/freighters/", function() use ($app, $controller) {

        });

        $app->get("/supercarriers/", function() use ($app, $controller) {

        });

        $app->get("/titans/", function() use ($app, $controller) {

        });

        $app->get("/t1/", function() use ($app, $controller) {

        });

        $app->get("/t2/", function() use ($app, $controller) {

        });

        $app->get("/t3/", function() use ($app, $controller) {

        });

        $app->get("/frigates/", function() use ($app, $controller) {

        });

        $app->get("/destroyers/", function() use ($app, $controller) {

        });

        $app->get("/cruisers/", function() use ($app, $controller) {

        });

        $app->get("/battlecruisers/", function() use ($app, $controller) {

        });

        $app->get("/battleships/", function() use ($app, $controller) {

        });

    });

    $app->group("/kills", function() use ($app) {
        $app->get("/solarSystem/:solarSystemID/(:extraParameters+)", function (int $solarSystemID, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/region/:regionID/(:extraParameters+)", function (int $regionID, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/character/:characterID/(:extraParameters+)", function (int $characterID, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/corporation/:corporationID/(:extraParameters+)", function (int $corporationID, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/alliance/:allianceID/(:extraParameters+)", function (int $allianceID, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/faction/:factionID/(:extraParameters+)", function (int $factionID, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/shipType/:shipTypeID/(:extraParameters+)", function (int $shipTypeID, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/group/:groupID/(:extraParameters+)", function (int $groupID, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/weaponType/:weaponTypeID/(:extraParameters+)", function (int $weaponTypeID, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/afterDate/:afterDate/(:extraParameters+)", function ($afterDate, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/beforeDate/:beforeDate/(:extraParameters+)", function ($beforeDate, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/betweenDates/:afterDate/:beforeDate/(:extraParameters+)", function ($afterDate, $beforeDate, $parameters = array()) use ($app, $controller) {

        });
    });

    $app->group("/losses", function() use ($app) {
        $app->get("/solarSystem/:solarSystemID/(:extraParameters+)", function (int $solarSystemID, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/region/:regionID/(:extraParameters+)", function (int $regionID, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/character/:characterID/(:extraParameters+)", function (int $characterID, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/corporation/:corporationID/(:extraParameters+)", function (int $corporationID, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/alliance/:allianceID/(:extraParameters+)", function (int $allianceID, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/faction/:factionID/(:extraParameters+)", function (int $factionID, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/shipType/:shipTypeID/(:extraParameters+)", function (int $shipTypeID, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/group/:groupID/(:extraParameters+)", function (int $groupID, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/weaponType/:weaponTypeID/(:extraParameters+)", function (int $weaponTypeID, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/afterDate/:afterDate/(:extraParameters+)", function ($afterDate, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/beforeDate/:beforeDate/(:extraParameters+)", function ($beforeDate, $parameters = array()) use ($app, $controller) {

        });

        $app->get("/betweenDates/:afterDate/:beforeDate/(:extraParameters+)", function ($afterDate, $beforeDate, $parameters = array()) use ($app, $controller) {

        });
    });

    $app->group("/stats", function() use ($app) {
        $app->get("/top10Characters/", function () use ($app, $controller) {

        });

        $app->get("/top10Corporations/", function () use ($app, $controller) {

        });

        $app->get("/top10Alliances/", function () use ($app, $controller) {

        });

        $app->get("/top10SolarSystems/", function () use ($app, $controller) {

        });

        $app->get("/top10Regions/", function () use ($app, $controller) {

        });

        $app->get("/mostValuableKillsLast7Days/", function () use ($app, $controller) {

        });

        $app->get("/sevenDayKillCount/", function () use ($app, $controller) {

        });

        $app->get("/currentlyActiveCharacters/", function () use ($app, $controller) {

        });

        $app->get("/currentlyActiveCorporations/", function () use ($app, $controller) {

        });

        $app->get("/currentlyActiveAlliances/", function () use ($app, $controller) {

        });

        $app->get("/currentlyActiveShipTypes/", function () use ($app, $controller) {

        });

        $app->get("/currentlyActiveSolarSystems/", function () use ($app, $controller) {

        });
    });

    $app->group("/search", function() use ($app) {
        $app->get("(/:searchType)/:searchTerm/", function ($searchType = null, $searchTerm = null) use ($app, $controller) {
            if (!$searchType)
                $searchType = array("faction", "alliance", "corporation", "character", "item", "system", "region");
        });
    });

    $app->group("/tools", function() use ($app) {
        $app->post("/calculateCrestHash/", function () use ($app) {

        });

        $app->post("/validateCrestUrl/", function () use ($app) {

        });
    });

    $app->group("/wars", function() use ($app) {
        $app->get("/count/", function () use ($app) {

        });

        $app->get("/wars/", function () use ($app) {

        });

        $app->get("/kills/:warID/", function ($warID) use ($app) {

        });
    });

    $app->group("/market", function() use ($app) {
        $app->get("/price/:typeID/", function ($typeID) use ($app) {

        });

        $app->get("/prices/:typeID/", function ($typeID) use ($app) {

        });
    });

    $app->group("/authed", function() use ($app) {

    });*/
});
