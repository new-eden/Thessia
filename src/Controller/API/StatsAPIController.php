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

    public function top10Characters() {
        $md5 = md5("top10CharactersStatsAPI");
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));

        $data = $this->collection->aggregate(array(
            array("\$match" => array("attackers.characterID" => array("\$gt" => 0))),
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

    public function top10Corporations() {
        $md5 = md5("top10CorporationsStatsAPI");
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));

        $data = $this->collection->aggregate(array(
            array("\$match" => array("attackers.corporationID" => array("\$gt" => 0))),
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

    public function top10Alliances() {
        $md5 = md5("top10AlliancesStatsAPI");
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));

        $data = $this->collection->aggregate(array(
            array("\$match" => array("attackers.allianceID" => array("\$gt" => 0))),
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

    public function top10SolarSystems() {
        $md5 = md5("top10SolarSystemsStatsAPI");
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));

        $data = $this->collection->aggregate(array(
            array("\$match" => array("solarSystemID" => array("\$gt" => 0))),
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

    public function top10Regions() {
        $md5 = md5("top10CRegionsStatsAPI");
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));

        $data = $this->collection->aggregate(array(
            array("\$match" => array("regionID" => array("\$gt" => 0))),
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

    public function mostValuableKillsOverTheLast7Days() {

    }

    public function sevenDayKillCount() {

    }

    public function countCurrentlyActiveEntities() {

    }

    public function currentlyActiveCharacters() {

    }

    public function currentlyActiveCorporations() {

    }

    public function currentlyActiveAlliances() {

    }

    public function currentlyActiveShipTypes() {

    }

    public function currentlyActiveSolarSystems() {

    }

    public function currentlyActiveRegions() {

    }
}