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

/**
 * Created by PhpStorm.
 * User: micha
 * Date: 15-08-2016
 * Time: 13:58
 */

namespace Thessia\Controller\API;


use Slim\App;
use Thessia\Middleware\Controller;

class StatsAPIController extends Controller {
    private $collection;
    private $characters;
    private $corporations;
    private $alliances;
    private $solarSystems;
    private $regions;
    private $cache;

    public function __construct(App $app) {
        parent::__construct($app);
        $this->cache = $this->container->get("cache");
        $this->collection = $this->mongo->selectCollection("thessia", "killmails");
        $this->characters = $this->mongo->selectCollection("thessia", "characters");
        $this->corporations = $this->mongo->selectCollection("thessia", "corporations");
        $this->alliances = $this->mongo->selectCollection("thessia", "alliances");
        $this->solarSystems = $this->mongo->selectCollection("ccp", "solarSystems");
        $this->regions = $this->mongo->selectCollection("ccp", "regions");
    }

    public function top10Characters(bool $allTime = false) {
        $md5 = md5("top10CharactersStatsAPI" . $allTime ? "allTime" : "7days");
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));

        if($allTime == true)
            $match = array("\$match" => array("attackers.characterID" => array("\$gt" => 0)));
        else
            $match = array("\$match" => array("killTime" => array("\$gte" => $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime("-7 days")))), "attackers.characterID" => array("\$gt" => 0)));

        $data = $this->collection->aggregate(array(
            $match,
            array("\$group" => array("_id" => "\$attackers.characterID", "count" => array("\$sum" => 1))),
            array("\$project" => array("_id" => 0, "count" => "\$count", "characterID"=> "\$_id")),
            array("\$sort" => array("count" => -1)),
            array("\$limit" => 10)
        ),
            array("allowDiskUse" => true)
        )->toArray();

        foreach($data as $key => $character) {
            $data[$key]["characterName"] = $this->characters->findOne(array("characterID" => $character["characterID"][0]))["characterName"];
            $data[$key]["characterID"] = $character["characterID"][0];
        }

        $this->cache->set($md5, $data, 3600);
        return $this->json($data);
    }

    public function top10Corporations(bool $allTime = false) {
        $md5 = md5("top10CorporationsStatsAPI" . $allTime ? "allTime" : "7days");
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));

        if($allTime == true)
            $match = array("\$match" => array("attackers.corporationID" => array("\$gt" => 0)));
        else
            $match = array("\$match" => array("killTime" => array("\$gte" => $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime("-7 days")))), "attackers.corporationID" => array("\$gt" => 0)));

        $data = $this->collection->aggregate(array(
            $match,
            array("\$group" => array("_id" => "\$attackers.corporationID", "count" => array("\$sum" => 1))),
            array("\$project" => array("_id" => 0, "count" => "\$count", "corporationID"=> "\$_id")),
            array("\$sort" => array("count" => -1)),
            array("\$limit" => 10)
        ),
            array("allowDiskUse" => true)
        )->toArray();

        foreach($data as $key => $corporation) {
            $data[$key]["corporationName"] = $this->corporations->findOne(array("corporationID" => $corporation["corporationID"][0]))["corporationName"];
            $data[$key]["corporationID"] = $corporation["corporationID"][0];
        }

        $this->cache->set($md5, $data, 3600);
        return $this->json($data);
    }

    public function top10Alliances(bool $allTime = false) {
        $md5 = md5("top10AlliancesStatsAPI" . $allTime ? "allTime" : "7days");
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));

        if($allTime == true)
            $match = array("\$match" => array("attackers.allianceID" => array("\$gt" => 0)));
        else
            $match = array("\$match" => array("killTime" => array("\$gte" => $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime("-7 days")))), "attackers.allianceID" => array("\$gt" => 0)));

        $data = $this->collection->aggregate(array(
            $match,
            array("\$group" => array("_id" => "\$attackers.allianceID", "count" => array("\$sum" => 1))),
            array("\$project" => array("_id" => 0, "count" => "\$count", "allianceID"=> "\$_id")),
            array("\$sort" => array("count" => -1)),
            array("\$limit" => 10)
        ),
            array("allowDiskUse" => true)
        )->toArray();

        foreach($data as $key => $alliance) {
            $data[$key]["allianceName"] = $this->alliances->findOne(array("allianceID" => $alliance["allianceID"][0]))["allianceName"];
            $data[$key]["allianceID"] = $alliance["allianceID"][0];
        }

        $this->cache->set($md5, $data, 3600);
        return $this->json($data);
    }

    public function top10SolarSystems(bool $allTime = false) {
        $md5 = md5("top10SolarSystemsStatsAPI" . $allTime ? "allTime" : "7days");
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));

        if($allTime == true)
            $match = array("\$match" => array("solarSystemID" => array("\$gt" => 0)));
        else
            $match = array("\$match" => array("killTime" => array("\$gte" => $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime("-7 days")))), "solarSystemID" => array("\$gt" => 0)));

        $data = $this->collection->aggregate(array(
            $match,
            array("\$group" => array("_id" => "\$solarSystemID", "count" => array("\$sum" => 1))),
            array("\$project" => array("_id" => 0, "count" => "\$count", "solarSystemID"=> "\$_id")),
            array("\$sort" => array("count" => -1)),
            array("\$limit" => 10)
        ),
            array("allowDiskUse" => true)
        )->toArray();

        foreach($data as $key => $solarSystem) {
            $data[$key]["solarSystemName"] = $this->solarSystems->findOne(array("solarSystemID" => $solarSystem["solarSystemID"]))["solarSystemName"];
        }

        $this->cache->set($md5, $data, 3600);
        return $this->json($data);
    }

    public function top10Regions(bool $allTime = false) {
        $md5 = md5("top10CRegionsStatsAPI" . $allTime ? "allTime" : "7days");
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));

        if($allTime == true)
            $match = array("\$match" => array("regionID" => array("\$gt" => 0)));
        else
            $match = array("\$match" => array("killTime" => array("\$gte" => $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime("-7 days")))), "regionID" => array("\$gt" => 0)));

        $data = $this->collection->aggregate(array(
            $match,
            array("\$group" => array("_id" => "\$regionID", "count" => array("\$sum" => 1))),
            array("\$project" => array("_id" => 0, "count" => "\$count", "regionID"=> "\$_id")),
            array("\$sort" => array("count" => -1)),
            array("\$limit" => 10)
        ),
            array("allowDiskUse" => true)
        )->toArray();

        foreach($data as $key => $region) {
            $data[$key]["regionName"] = $this->regions->findOne(array("regionID" => $region["regionID"]))["regionName"];
        }

        $this->cache->set($md5, $data, 3600);
        return $this->json($data);
    }

    public function mostValuableKillsOverTheLast7Days(int $limit = 10) {
        $md5 = md5("mostValuableKillsOverTheLast7Days");
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));

        $data = $this->collection->find(array("killTime" => array("\$gte" => $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime("-7 days"))))), array("sort" => array("totalValue" => -1), "limit" => $limit))->toArray();

        $this->cache->set($md5, $data, 3600);
        return $this->json($data);
    }

    public function sevenDayKillCount() {
        $md5 = md5("sevenDayKillCount");
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));

        $data["sevenDayKillCount"] = $this->collection->count(array("killTime" => array("\$gte" => $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime("-7 days"))))));

        $this->cache->set($md5, $data, 60);
        return $this->json($data);

    }

    public function activeEntities() {
        $data = array(
            "activeCharacters" => $this->activeCharacters(false),
            "activeCorporations" => $this->activeCorporations(false),
            "activeAlliances" => $this->activeAlliances(false),
            "activeShipTypes" => $this->activeShipTypes(false),
            "activeSolarSystems" => $this->activeSolarSystems(false),
            "activeRegions" => $this->activeRegions(false),
        );

        return $this->json($data);
    }

    public function activeCharacters($json = true) {
        $md5 = md5("activeCharacters");
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));


        $match = array("\$match" => array("killTime" => array("\$gte" => $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime("-7 days")))), "attackers.characterID" => array("\$gt" => 0)));
        $data = $this->collection->aggregate(array(
            $match,
            array("\$group" => array("_id" => "\$attackers.characterID")),
            array("\$group" => array("_id" => 1, "count" => array("\$sum" => 1)))
        ),
            array("allowDiskUse" => true)
        )->toArray();

        $returnData["activeCharacters"] = count($data);

        $this->cache->set($md5, count($data), 60);

        if($json == false)
            return count($data);

        return $this->json($returnData);
    }

    public function activeCorporations($json = true) {
        $md5 = md5("activeCorporations");
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));


        $match = array("\$match" => array("killTime" => array("\$gte" => $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime("-7 days")))), "attackers.corporationID" => array("\$gt" => 0)));
        $data = $this->collection->aggregate(array(
            $match,
            array("\$group" => array("_id" => "\$attackers.corporationID")),
            array("\$group" => array("_id" => 1, "count" => array("\$sum" => 1)))
        ),
            array("allowDiskUse" => true)
        )->toArray();

        $returnData["activeCorporations"] = count($data);
        $this->cache->set($md5, count($data), 60);
        if($json == false)
            return count($data);

        return $this->json($returnData);

    }

    public function activeAlliances($json = true) {
        $md5 = md5("activeAlliances");
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));


        $match = array("\$match" => array("killTime" => array("\$gte" => $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime("-7 days")))), "attackers.allianceID" => array("\$gt" => 0)));
        $data = $this->collection->aggregate(array(
            $match,
            array("\$group" => array("_id" => "\$attackers.allianceID")),
            array("\$group" => array("_id" => 1, "count" => array("\$sum" => 1)))
        ),
            array("allowDiskUse" => true)
        )->toArray();

        $returnData["activeAlliances"] = count($data);
        $this->cache->set($md5, count($data), 60);
        if($json == false)
            return count($data);

        return $this->json($returnData);

    }

    public function activeShipTypes($json = true) {
        $md5 = md5("activeShipTypes");
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));

        $match = array("\$match" => array("killTime" => array("\$gte" => $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime("-7 days")))), "attackers.shipTypeID" => array("\$gt" => 0)));
        $data = $this->collection->aggregate(array(
            $match,
            array("\$group" => array("_id" => "\$attackers.shipTypeID")),
            array("\$group" => array("_id" => 1, "count" => array("\$sum" => 1)))
        ),
            array("allowDiskUse" => true)
        )->toArray();

        $returnData["activeShipTypes"] = count($data);
        $this->cache->set($md5, count($data), 60);
        if($json == false)
            return count($data);

        return $this->json($returnData);

    }

    public function activeSolarSystems($json = true) {
        $md5 = md5("activeSolarSystems");
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));

        $match = array("\$match" => array("killTime" => array("\$gte" => $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime("-7 days")))), "solarSystemID" => array("\$gt" => 0)));
        $data = $this->collection->aggregate(array(
            $match,
            array("\$group" => array("_id" => "\$solarSystemID")),
            array("\$group" => array("_id" => 1, "count" => array("\$sum" => 1)))
        ),
            array("allowDiskUse" => true)
        )->toArray();

        $returnData["activeSolarSystems"] = count($data);
        $this->cache->set($md5, count($data), 60);
        if($json == false)
            return count($data);

        return $this->json($returnData);

    }

    public function activeRegions($json = true) {
        $md5 = md5("activeRegions");
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));

        $match = array("\$match" => array("killTime" => array("\$gte" => $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime("-7 days")))), "regionID" => array("\$gt" => 0)));
        $data = $this->collection->aggregate(array(
            $match,
            array("\$group" => array("_id" => "\$regionID")),
            array("\$group" => array("_id" => 1, "count" => array("\$sum" => 1)))
        ),
            array("allowDiskUse" => true)
        )->toArray();

        $returnData["activeRegions"] = count($data);
        $this->cache->set($md5, count($data), 60);
        if($json == false)
            return count($data);

        return $this->json($returnData);

    }
}