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

$app->group("/api", function() use ($app) {
    $app->group("/character", function() use ($app) {
        $controller = new \Thessia\Controller\API\CharacterAPIController($app);
        $app->get("/count/", $controller("characterCount"));
        $app->get("/information/{characterID:[0-9]+}/", $controller("characterInformation"));
        $app->get("/find/{searchTerm:[A-Za-z0-9]+}/", $controller("findCharacter"));

        $app->group("/top", function() use ($app, $controller) {
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
            $app->get("/corporations/{allianceID:[0-9]+}/[{limit:[0-9]+}/]", $controller("topCorporations"));
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
    });

    $app->group("/system", function() use ($app) {
        $controller = new \Thessia\Controller\API\SolarSystemAPIController($app);
        $app->get("/count/", $controller("solarSystemCount"));
        $app->get("/information/{typeID:[0-9]+}/", $controller("solarSystemInformation"));
        $app->get("/find/{searchTerm:[^\\/]+}/", $controller("findSolarSystem"));
    });

    $app->group("/region", function() use ($app) {
        $controller = new \Thessia\Controller\API\RegionAPIController($app);
        $app->get("/count/", $controller("regionCount"));
        $app->get("/information/{typeID:[0-9]+}/", $controller("regionInformation"));
        $app->get("/find/{searchTerm:[^\\/]+}/", $controller("findRegion"));
    });

    $app->group("/constellation", function() use ($app) {
        $controller = new \Thessia\Controller\API\ConstellationAPIController($app);
        $app->get("/count/", $controller("constellationCount"));
        $app->get("/information/{typeID:[0-9]+}/", $controller("constellationInformation"));
        $app->get("/find/{searchTerm:[^\\/]+}/", $controller("findConstellation"));
    });

    $app->group("/celestial", function() use ($app) {
        $controller = new \Thessia\Controller\API\CelestialAPIController($app);
        $app->get("/count/", $controller("celestialCount"));
        $app->get("/information/{typeID:[0-9]+}/", $controller("celestialInformation"));
        $app->get("/find/{searchTerm:[^\\/]+}/", $controller("findCelestial"));
    });

    $app->group("/kill", function() use ($app) {
        //$app->post("/add/");
        //$app->get("/count/");
        //$app->get("/mail/killID/{killID:[0-9]+}/");
        //$app->get("/mail/crestHash/{crestHash:[a-zA-Z0-9]+}/");
    });

    $app->group("/kills", function() use ($app) {
        $controller = new \Thessia\Controller\API\KillsAPIController($app);
        $app->get("/solarSystem/{solarSystemID:[0-9]+}/[{extraParams:.*}]", $controller("getSolarSystem"));
        $app->get("/region/{regionID:[0-9]+}/[{extraParams:.*}]", $controller("getRegion"));
        $app->get("/character/{characterID:[0-9]+}/[{extraParams:.*}]", $controller("getCharacter"));
        $app->get("/corporation/{corporationID:[0-9]+}/[{extraParams:.*}]", $controller("getCorporation"));
        $app->get("/alliance/{allianceID:[0-9]+}/[{extraParams:.*}]", $controller("getAlliance"));
        $app->get("/faction/{factionID:[0-9]+}/[{extraParams:.*}]", $controller("getFaction"));
        $app->get("/shipType/{shipTypeID:[0-9]+}/[{extraParams:.*}]", $controller("getShipType"));
        $app->get("/weaponType/{weaponTypeID:[0-9]+}/[{extraParams:.*}]", $controller("getWeaponType"));
        $app->get("/afterDate/{afterDate:[^\\/]+}/[{extraParams:.*}]", $controller("getAfterDate"));
        $app->get("/beforeDate/{beforeDate:[^\\/]+}/[{extraParams:.*}]", $controller("getBeforeDate"));
        $app->get("/betweenDates/{afterDate:[^\\/]+}/{beforeDate:[^\\/]+}/[{extraParams:.*}]", $controller("getBetweenDates"));
    });

    $app->group("/losses", function() use ($app) {
        $controller = new \Thessia\Controller\API\LossesAPIController($app);
        $app->get("/character/{characterID:[0-9]+}/[{extraParams:.*}]", $controller("getCharacter"));
        $app->get("/corporation/{corporationID:[0-9]+}/[{extraParams:.*}]", $controller("getCorporation"));
        $app->get("/alliance/{allianceID:[0-9]+}/[{extraParams:.*}]", $controller("getAlliance"));
        $app->get("/faction/{factionID:[0-9]+}/[{extraParams:.*}]", $controller("getFaction"));
        $app->get("/shipType/{shipTypeID:[0-9]+}/[{extraParams:.*}]", $controller("getShipType"));
    });

    /*
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
