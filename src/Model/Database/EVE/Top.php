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

    /**
     * Top constructor.
     * @param Client $mongo
     */
    public function __construct(Client $mongo) {
        $this->mongo = $mongo;

        $this->alliances = $this->mongo->selectCollection("thessia", "alliances");
        $this->corporations = $this->mongo->selectCollection("thessia", "corporations");
        $this->characters = $this->mongo->selectCollection("thessia", "characters");
        $this->killmails = $this->mongo->selectCollection("thessia", "killmails");
        $this->shipTypes = $this->mongo->selectCollection("ccp", "typeIDs");
        $this->solarSystems = $this->mongo->selectCollection("ccp", "solarSystems");
        $this->regions = $this->mongo->selectCollection("ccp", "regions");
    }

    public function topCharacters(string $victimType, int $typeID, int $limit = 10) {
        $data = $this->killmails->aggregate(array(
            array('$match' => array("victim.{$victimType}" => $typeID, "victim.characterID" => array("\$ne" => 0))),
            array('$group' => array("_id" => '$victim.characterID', "count" => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "characterID" => '$_id')),
            array('$sort' => array("count" => -1)),
            array('$limit' => $limit)
        ))->toArray();

        foreach($data as $key => $character)
            $data[$key]["characterName"] = $this->characters->findOne(array("characterID" => $character["characterID"]))["characterName"];

        return $data;
    }

    public function topCorporations(string $victimType, int $typeID, int $limit = 10) {
        $data = $this->killmails->aggregate(array(
            array('$match' => array("victim.{$victimType}" => $typeID, "victim.corporationID" => array("\$ne" => 0))),
            array('$group' => array("_id" => '$victim.corporationID', "count" => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "corporationID" => '$_id')),
            array('$sort' => array("count" => -1)),
            array('$limit' => $limit)
        ))->toArray();
        foreach($data as $key => $corporation)
            $data[$key]["corporationName"] = $this->corporations->findOne(array("corporationID" => $corporation["corporationID"]))["corporationName"];

        return $data;
    }

    public function topAlliances(string $victimType, int $typeID, int $limit = 10) {
        $data = $this->killmails->aggregate(array(
            array('$match' => array("victim.{$victimType}" => $typeID, "victim.allianceID" => array("\$ne" => 0))),
            array('$group' => array("_id" => '$victim.allianceID', "count" => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "allianceID" => '$_id')),
            array('$sort' => array("count" => -1)),
            array('$limit' => $limit)
        ))->toArray();
        foreach($data as $key => $alliance)
            $data[$key]["allianceName"] = $this->alliances->findOne(array("allianceID" => $alliance["allianceID"]))["allianceName"];

        return $data;
    }

    public function topShips(string $victimType, int $typeID, int $limit = 10) {
        $data = $this->killmails->aggregate(array(
            array('$match' => array("victim.{$victimType}" => $typeID)),
            array('$group' => array("_id" => '$victim.shipTypeID', "count" => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "shipTypeID" => '$_id')),
            array('$sort' => array("count" => -1)),
            array('$limit' => $limit)
        ))->toArray();
        foreach($data as $key => $shipType)
            $data[$key]["shipTypeName"] = $this->shipTypes->findOne(array("typeID" => $shipType["shipTypeID"]))["name"]["en"];

        return $data;
    }

    public function topSystems(string $victimType, int $typeID, int $limit = 10) {
        $data = $this->killmails->aggregate(array(
            array('$match' => array("victim.{$victimType}" => $typeID)),
            array('$group' => array("_id" => '$solarSystemID', "count" => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "solarSystemID" => '$_id')),
            array('$sort' => array("count" => -1)),
            array('$limit' => $limit)
        ))->toArray();
        foreach($data as $key => $solarSystem)
            $data[$key]["solarSystemName"] = $this->solarSystems->findOne(array("solarSystemID" => $solarSystem["solarSystemID"]))["solarSystemName"];

        return $data;
    }

    public function topRegions(string $victimType, int $typeID, int $limit = 10) {
        $data = $this->killmails->aggregate(array(
            array('$match' => array("victim.{$victimType}" => $typeID)),
            array('$group' => array("_id" => '$regionID', "count" => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "regionID" => '$_id')),
            array('$sort' => array("count" => -1)),
            array('$limit' => $limit)
        ))->toArray();
        foreach($data as $key => $region)
            $data[$key]["regionName"] = $this->regions->findOne(array("regionID" => $region["regionID"]))["regionName"];

        return $data;
    }
}