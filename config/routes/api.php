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
        $app->get("/information/{solarSystemID:[0-9]+}/", $controller("celestialInformation"));
        $app->get("/find/{searchTerm:[^\\/]+}/", $controller("findCelestial"));
    });

    $app->group("/kill", function() use ($app) {
        $controller = new \Thessia\Controller\API\KillAPIController($app);
        $app->post("/add/", $controller("addKill"));
        $app->get("/count/", $controller("getKillCount"));
        $app->get("/mail/killID/{killID:[0-9]+}/", $controller("getKillByID"));
        $app->get("/mail/hash/{hash:[a-zA-Z0-9]+}/", $controller("getKillByHash"));
        $app->get("/dump/{date:[0-9]+}/", $controller("getKillsByDate"));
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

    $app->group("/killlist", function() use ($app) {
        $controller = new \Thessia\Controller\API\KillListAPIController($app);
        $app->get("/latest/[{page:[0-9]+}/]", $controller("getLatest"));
        $app->get("/bigkills/[{page:[0-9]+}/]", $controller("getBigKills"));
        $app->get("/wspace/[{page:[0-9]+}/]", $controller("getWSpace"));
        $app->get("/highsec/[{page:[0-9]+}/]", $controller("getHighSec"));
        $app->get("/lowsec/[{page:[0-9]+}/]", $controller("getLowSec"));
        $app->get("/nullsec/[{page:[0-9]+}/]", $controller("getNullSec"));
        $app->get("/solo/[{page:[0-9]+}/]", $controller("getSolo"));
        $app->get("/npc/[{page:[0-9]+}/]", $controller("getNPC"));
        $app->get("/5b/[{page:[0-9]+}/]", $controller("get5b"));
        $app->get("/10b/[{page:[0-9]+}/]", $controller("get10b"));
        $app->get("/citadels/[{page:[0-9]+}/]", $controller("getCitadels"));
        $app->get("/t1/[{page:[0-9]+}/]", $controller("getT1"));
        $app->get("/t2/[{page:[0-9]+}/]", $controller("getT2"));
        $app->get("/t3/[{page:[0-9]+}/]", $controller("getT3"));
        $app->get("/frigates/[{page:[0-9]+}/]", $controller("getFrigates"));
        $app->get("/destroyers/[{page:[0-9]+}/]", $controller("getDestroyers"));
        $app->get("/cruisers/[{page:[0-9]+}/]", $controller("getCruisers"));
        $app->get("/battlecruisers/[{page:[0-9]+}/]", $controller("getBattleCruisers"));
        $app->get("/battleships/[{page:[0-9]+}/]", $controller("getBattleShips"));
        $app->get("/capitals/[{page:[0-9]+}/]", $controller("getCapitals"));
        $app->get("/freighters/[{page:[0-9]+}/]", $controller("getFreighters"));
        $app->get("/supercarriers/[{page:[0-9]+}/]", $controller("getSuperCarriers"));
        $app->get("/titans/[{page:[0-9]+}/]", $controller("getTitans"));
    });

    $app->group("/stats", function() use ($app) {
        $controller = new \Thessia\Controller\API\StatsAPIController($app);
        $app->get("/top10characters/[{allTime:[0-1]}/]", $controller("top10Characters"));
        $app->get("/top10corporations/[{allTime:[0-1]}/]", $controller("top10Corporations"));
        $app->get("/top10alliances/[{allTime:[0-1]}/]", $controller("top10Alliances"));
        $app->get("/top10solarsystems/[{allTime:[0-1]}/]", $controller("top10SolarSystems"));
        $app->get("/top10regions/[{allTime:[0-1]}/]", $controller("top10Regions"));
        $app->get("/mostvaluablekillslast7days/[{limit:[0-9]+}/]", $controller("mostValuableKillsOverTheLast7Days"));
        $app->get("/sevendaykillcount/", $controller("sevenDayKillCount"));
        $app->get("/activeentities/[{allTime:[0-1]}/]", $controller("activeEntities"));
        $app->get("/activecharacters/[{allTime:[0-1]}/]", $controller("activeCharacters"));
        $app->get("/activecorporations/[{allTime:[0-1]}/]", $controller("activeCorporations"));
        $app->get("/activealliances/[{allTime:[0-1]}/]", $controller("activeAlliances"));
        $app->get("/activeshiptypes/[{allTime:[0-1]}/]", $controller("activeShipTypes"));
        $app->get("/activesolarsystems/[{allTime:[0-1]}/]", $controller("activeSolarSystems"));
        $app->get("/activeregions/[{allTime:[0-1]}/]", $controller("activeRegions"));
    });

    $app->group("/search", function() use ($app) {
        $controller = new \Thessia\Controller\API\SearchAPIController($app);
        $app->get("/faction/{searchTerm:[A-Za-z0-9]+}/", $controller("findFaction"));
        $app->get("/alliance/{searchTerm:[A-Za-z0-9]+}/", $controller("findAlliance"));
        $app->get("/corporation/{searchTerm:[A-Za-z0-9]+}/", $controller("findCorporation"));
        $app->get("/character/{searchTerm:[A-Za-z0-9]+}/", $controller("findCharacter"));
        $app->get("/item/{searchTerm:[A-Za-z0-9]+}/", $controller("findItem"));
        $app->get("/system/{searchTerm:[A-Za-z0-9]+}/", $controller("findSolarSystem"));
        $app->get("/region/{searchTerm:[A-Za-z0-9]+}/", $controller("findRegion"));
        $app->get("/celestial/{searchTerm:[A-Za-z0-9]+}/", $controller("findCelestial"));
    });

    $app->group("/wars", function() use ($app) {
        $controller = new \Thessia\Controller\API\WarsAPIController($app);
        $app->get("/count/", $controller("count"));
        $app->get("/wars/[{warID:[0-9]+}/]", $controller("wars"));
        $app->get("/kills/{warID:[0-9]+}/[{page:[0-9]+}/]", $controller("warMails"));
    });

    $app->group("/image", function() use ($app) {
        $controller = new \Thessia\Controller\API\ImageAPIController($app);
        $app->get("/alliance/{allianceID:[0-9]+}_{imageSize:[0-9]+}.png", $controller("getAllianceImage"));
        $app->get("/corporation/{corporationID:[0-9]+}_{imageSize:[0-9]+}.png", $controller("getCorporationImage"));
        $app->get("/character/{characterID:[0-9]+}_{imageSize:[0-9]+}.jpg", $controller("getCharacterImage"));
        $app->get("/inventory/{inventoryID:[0-9]+}_{imageSize:[0-9]+}.jpg", $controller("getInventoryImage"));
        $app->get("/ship/{shipID:[0-9]+}_{imageSize:[0-9]+}.jpg", $controller("getShipImage"));
    });

    $app->group("/battlereport", function() use ($app) {
        $controller = new \Thessia\Controller\API\BattleReportAPIController($app);
        $app->get("/battles/[{page:[0-9]+}/]", $controller("getBattles"));
        $app->get("/battle/{battleID:[a-zA-Z0-9]+}/", $controller("getBattle"));
    });
});
