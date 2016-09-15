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


namespace Thessia\Model\Database\EVE;


use MongoDB\Client;
use Thessia\Lib\Cache;

class Top {
    /**
     * @var Client
     */
    private $mongo;
    /**
     * @var \MongoDB\Collection
     */
    private $alliances;
    /**
     * @var \MongoDB\Collection
     */
    private $corporations;
    /**
     * @var \MongoDB\Collection
     */
    private $characters;
    /**
     * @var \MongoDB\Collection
     */
    private $killmails;
    private $cache;

    /**
     * Top constructor.
     * @param Client $mongo
     * @param Cache $cache
     */
    public function __construct(Client $mongo, Cache $cache) {
        $this->mongo = $mongo;
        $this->cache = $cache;
        $this->alliances = $this->mongo->selectCollection("thessia", "alliances");
        $this->corporations = $this->mongo->selectCollection("thessia", "corporations");
        $this->characters = $this->mongo->selectCollection("thessia", "characters");
        $this->killmails = $this->mongo->selectCollection("thessia", "killmails");
        $this->shipTypes = $this->mongo->selectCollection("ccp", "typeIDs");
        $this->solarSystems = $this->mongo->selectCollection("ccp", "solarSystems");
        $this->regions = $this->mongo->selectCollection("ccp", "regions");
    }

    public function topCharacters(string $attackerType, int $typeID, int $limit = 10) {
        $md5 = md5("topCharacters" . $attackerType . $typeID . $limit);
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        $data = $this->killmails->aggregate(array(
            array('$match' => array("attackers.{$attackerType}" => $typeID, "attackers.characterID" => array('$ne' => 0))),
            array('$unwind' => '$attackers'),
            array('$match' => array("attackers.{$attackerType}" => $typeID)),
            array('$group' => array("_id" => '$attackers.characterID', "count" => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "id" => '$_id')),
            array('$sort' => array("count" => -1)),
            array('$limit' => $limit)
        ))->toArray();

        foreach($data as $key => $character)
            $data[$key]["name"] = $this->characters->findOne(array("characterID" => $character["id"]))["characterName"];


        $this->cache->set($md5, $data, 3600);
        return $data;
    }

    public function topCorporations(string $attackerType, int $typeID, int $limit = 10) {
        $md5 = md5("topCorporations" . $attackerType . $typeID . $limit);
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        $data = $this->killmails->aggregate(array(
            array('$match' => array("attackers.{$attackerType}" => $typeID, "attackers.corporationID" => array('$ne' => 0))),
            array('$unwind' => '$attackers'),
            array('$match' => array("attackers.{$attackerType}" => $typeID)),
            array('$group' => array("_id" => '$attackers.corporationID', "count" => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "id" => '$_id')),
            array('$sort' => array("count" => -1)),
            array('$limit' => $limit)
        ))->toArray();
        foreach($data as $key => $corporation)
            $data[$key]["name"] = $this->corporations->findOne(array("corporationID" => $corporation["id"]))["corporationName"];

        $this->cache->set($md5, $data, 3600);
        return $data;
    }

    public function topAlliances(string $attackerType, int $typeID, int $limit = 10) {
        $md5 = md5("topAlliances" . $attackerType . $typeID . $limit);
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        $data = $this->killmails->aggregate(array(
            array('$match' => array("attackers.{$attackerType}" => $typeID, "attackers.allianceID" => array('$ne' => 0))),
            array('$unwind' => '$attackers'),
            array('$match' => array("attackers.{$attackerType}" => $typeID)),
            array('$group' => array("_id" => '$attackers.allianceID', "count" => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "id" => '$_id')),
            array('$sort' => array("count" => -1)),
            array('$limit' => $limit)
        ))->toArray();
        foreach($data as $key => $alliance)
            $data[$key]["name"] = $this->alliances->findOne(array("allianceID" => $alliance["id"]))["allianceName"];

        $this->cache->set($md5, $data, 3600);
        return $data;
    }

    public function topShips(string $attackerType, int $typeID, int $limit = 10) {
        $md5 = md5("topShips" . $attackerType . $typeID . $limit);
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        $data = $this->killmails->aggregate(array(
            array('$match' => array("attackers.{$attackerType}" => $typeID)),
            array('$unwind' => '$attackers'),
            array('$match' => array("attackers.{$attackerType}" => $typeID)),
            array('$group' => array("_id" => '$attackers.shipTypeID', "count" => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "id" => '$_id')),
            array('$sort' => array("count" => -1)),
            array('$limit' => $limit)
        ))->toArray();
        foreach($data as $key => $shipType)
            $data[$key]["name"] = trim($this->shipTypes->findOne(array("typeID" => $shipType["id"]))["name"]["en"]);

        $this->cache->set($md5, $data, 3600);
        return $data;
    }

    public function topSystems(string $attackerType, int $typeID, int $limit = 10) {
        $md5 = md5("topSystems" . $attackerType . $typeID . $limit);
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        $data = $this->killmails->aggregate(array(
            array('$match' => array("attackers.{$attackerType}" => $typeID)),
            array('$unwind' => '$attackers'),
            array('$match' => array("attackers.{$attackerType}" => $typeID)),
            array('$group' => array("_id" => '$solarSystemID', "count" => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "id" => '$_id')),
            array('$sort' => array("count" => -1)),
            array('$limit' => $limit)
        ))->toArray();
        foreach($data as $key => $solarSystem)
            $data[$key]["name"] = trim($this->solarSystems->findOne(array("solarSystemID" => $solarSystem["id"]))["solarSystemName"]);

        $this->cache->set($md5, $data, 3600);
        return $data;
    }

    public function topRegions(string $attackerType, int $typeID, int $limit = 10) {
        $md5 = md5("topRegions" . $attackerType . $typeID . $limit);
        if ($this->cache->exists($md5))
            return $this->cache->get($md5);

        $data = $this->killmails->aggregate(array(
            array('$match' => array("attackers.{$attackerType}" => $typeID)),
            array('$unwind' => '$attackers'),
            array('$match' => array("attackers.{$attackerType}" => $typeID)),
            array('$group' => array("_id" => '$regionID', "count" => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "id" => '$_id')),
            array('$sort' => array("count" => -1)),
            array('$limit' => $limit)
        ))->toArray();
        foreach($data as $key => $region)
            $data[$key]["name"] = trim($this->regions->findOne(array("regionID" => $region["id"]))["regionName"]);

        $this->cache->set($md5, $data, 3600);
        return $data;
    }
}