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

namespace Thessia\Tasks\Cron;

use League\Container\Container;
use MongoDB\BSON\UTCDatetime;
use MongoDB\Collection;
use Monolog\Logger;

class UpdateCacheables {
    private $container;
    public function perform() {
        $log = $this->container->get("log");
        $startTime = time();

        // 7 Day running
        $log->addInfo("Updating Top10 Characters");
        $this->updateTop10Characters(false);
        $log->addInfo("Updating Top10 Corporations");
        $this->updateTop10Corporation(false);
        $log->addInfo("Updating Top10 Alliances");
        $this->updateTop10Alliances(false);
        $log->addInfo("Updating Top10 SolarSystems");
        $this->updateTop10SolarSystems(false);
        $log->addInfo("Updating Top10 Regions");
        $this->updateTop10Regions(false);

        exit;
    }

    /**
     * Defines how often the cronjob runs, every 1 second, every 60 seconds, every 86400 seconds, etc.
     */
    public static function getRunTimes()
    {
        return 900;
    }

    private function updateTop10Characters(bool $allTime = false) {
        $mongo = $this->container->get("mongo");
        $collection = $mongo->selectCollection("thessia", "killmails");
        $characters = $mongo->selectCollection("thessia", "characters");
        $cache = $this->container->get("cache");

        $md5 = md5("top10CharactersStatsAPI" . $allTime);

        if($allTime == true)
            $match = array("\$match" => array("attackers.characterID" => array("\$ne" => 0)));
        else
            $match = array("\$match" => array("killTime" => array("\$gte" => $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime("-7 days")))), "atta  ckers.characterID" => array("\$ne" => 0)));

        $data = $collection->aggregate(array(
            $match,
            array("\$unwind" => "\$attackers"),
            array("\$group" => array("_id" => "\$attackers.characterID", "count" => array("\$sum" => 1))),
            array("\$project" => array("_id" => 0, "count" => "\$count", "characterID"=> "\$_id")),
            array("\$sort" => array("count" => -1)),
            array("\$limit" => 10)
        ),
            array("allowDiskUse" => true, "maxTimeMS" => 240000)
        )->toArray();

        foreach($data as $key => $character) {
            $count = $data[$key]["count"];
            $data[$key] = $characters->findOne(array("characterID" => $character["characterID"]));
            $data[$key]["count"] = $count;
        }

        $cache->set($md5, $data, 3600);
    }

    private function updateTop10Corporation(bool $allTime = false) {
        $mongo = $this->container->get("mongo");
        $collection = $mongo->selectCollection("thessia", "killmails");
        $corporations = $mongo->selectCollection("thessia", "corporations");
        $cache = $this->container->get("cache");

        $md5 = md5("top10CorporationsStatsAPI" . $allTime);

        if($allTime == true)
            $match = array("\$match" => array("attackers.corporationID" => array("\$ne" => 0)));
        else
            $match = array("\$match" => array("killTime" => array("\$gte" => $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime("-7 days")))), "attackers.corporationID" => array("\$ne" => 0)));

        $data = $collection->aggregate(array(
            $match,
            array("\$unwind" => "\$attackers"),
            array("\$group" => array("_id" => "\$attackers.corporationID", "count" => array("\$sum" => 1))),
            array("\$project" => array("_id" => 0, "count" => "\$count", "corporationID"=> "\$_id")),
            array("\$sort" => array("count" => -1)),
            array("\$limit" => 10),
        ),
            array("allowDiskUse" => true, "maxTimeMS" => 240000)
        )->toArray();

        foreach($data as $key => $corporation) {
            $count = $data[$key]["count"];
            $data[$key] = $corporations->findOne(array("corporationID" => $corporation["corporationID"]));
            $data[$key]["count"] = $count;
        }

        $cache->set($md5, $data, 3600);
    }

    private function updateTop10Alliances(bool $allTime = false) {
        $mongo = $this->container->get("mongo");
        $collection = $mongo->selectCollection("thessia", "killmails");
        $alliances = $mongo->selectCollection("thessia", "alliances");
        $cache = $this->container->get("cache");

        $md5 = md5("top10AlliancesStatsAPI" . $allTime);

        if($allTime == true)
            $match = array("\$match" => array("attackers.allianceID" => array("\$ne" => 0)));
        else
            $match = array("\$match" => array("killTime" => array("\$gte" => $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime("-7 days")))), "attackers.allianceID" => array("\$ne" => 0)));

        $data = $collection->aggregate(array(
            $match,
            array("\$unwind" => "\$attackers"),
            array("\$group" => array("_id" => "\$attackers.allianceID", "count" => array("\$sum" => 1))),
            array("\$project" => array("_id" => 0, "count" => "\$count", "allianceID"=> "\$_id")),
            array("\$sort" => array("count" => -1)),
            array("\$limit" => 10)
        ),
            array("allowDiskUse" => true, "maxTimeMS" => 240000)
        )->toArray();

        foreach($data as $key => $alliance) {
            $count = $data[$key]["count"];
            $data[$key] = $alliances->findOne(array("allianceID" => $alliance["allianceID"]), array("projection" => array("_id" => 0, "corporations" => 0, "description" => 0)));
            $data[$key]["count"] = $count;
        }

        $cache->set($md5, $data, 3600);
    }

    private function updateTop10SolarSystems(bool $allTime = false) {
        $mongo = $this->container->get("mongo");
        $collection = $mongo->selectCollection("thessia", "killmails");
        $solarSystems = $mongo->selectCollection("ccp", "solarSystems");
        $cache = $this->container->get("cache");
        
        $md5 = md5("top10SolarSystemsStatsAPI" . $allTime);

        if($allTime == true)
            $match = array("\$match" => array("solarSystemID" => array("\$ne" => 0)));
        else
            $match = array("\$match" => array("killTime" => array("\$gte" => $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime("-7 days")))), "solarSystemID" => array("\$ne" => 0)));

        $data = $collection->aggregate(array(
            $match,
            array("\$group" => array("_id" => "\$solarSystemID", "count" => array("\$sum" => 1))),
            array("\$project" => array("_id" => 0, "count" => "\$count", "solarSystemID"=> "\$_id")),
            array("\$sort" => array("count" => -1)),
            array("\$limit" => 10)
        ),
            array("allowDiskUse" => true, "maxTimeMS" => 240000)
        )->toArray();

        foreach($data as $key => $solarSystem) {
            $data[$key]["solarSystemName"] = $solarSystems->findOne(array("solarSystemID" => $solarSystem["solarSystemID"]))["solarSystemName"];
        }

        $cache->set($md5, $data, 3600);
    }

    private function updateTop10Regions(bool $allTime = false) {
        $mongo = $this->container->get("mongo");
        $collection = $mongo->selectCollection("thessia", "killmails");
        $regions = $mongo->selectCollection("ccp", "regions");
        $cache = $this->container->get("cache");

        $md5 = md5("top10CRegionsStatsAPI" . $allTime);

        if($allTime == true)
            $match = array("\$match" => array("regionID" => array("\$ne" => 0)));
        else
            $match = array("\$match" => array("killTime" => array("\$gte" => $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime("-7 days")))), "regionID" => array("\$ne" => 0)));

        $data = $collection->aggregate(array(
            $match,
            array("\$group" => array("_id" => "\$regionID", "count" => array("\$sum" => 1))),
            array("\$project" => array("_id" => 0, "count" => "\$count", "regionID"=> "\$_id")),
            array("\$sort" => array("count" => -1)),
            array("\$limit" => 10)
        ),
            array("allowDiskUse" => true, "maxTimeMS" => 240000)
        )->toArray();

        foreach($data as $key => $region) {
            $data[$key]["regionName"] = $regions->findOne(array("regionID" => $region["regionID"]))["regionName"];
        }

        $cache->set($md5, $data, 3600);
    }

    /**
     * @param $dateTime
     * @return UTCDatetime
     */
    private function makeTimeFromDateTime($dateTime): UTCDatetime {
        $unixTime = strtotime($dateTime);
        $milliseconds = $unixTime * 1000;

        return new UTCDatetime($milliseconds);
    }

    /**
     * @param $unixTime
     * @return UTCDatetime
     */
    private function makeTimeFromUnixTime($unixTime): UTCDatetime {
        $milliseconds = $unixTime * 1000;
        return new UTCDatetime($milliseconds);
    }

    public function setUp()
    {
        $this->container = getContainer();
    }

    public function tearDown()
    {

    }
}
