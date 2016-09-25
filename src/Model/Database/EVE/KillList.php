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

use DateTime;
use MongoDB\Client;
use Thessia\Lib\Cache;
use Thessia\Lib\Config;

class KillList extends Participants {
    public function __construct(Config $config, Client $mongodb, Cache $cache) {
        parent::__construct($config, $mongodb, $cache);

    }

    public function getLatest($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);
        $killData = $this->collection->find(array(), array("sort" => array("killTime" => -1), "projection" => array("_id" => 0, "items" => 0, "osmium" => 0), "skip" => $offset, "limit" => $limit))->toArray();
        foreach($killData as $key => $value)
            $killData[$key]["killTime"] = date(DateTime::ISO8601, $value["killTime"]->__toString() / 1000);

        return $killData;
    }

    public function getBigKills($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $typeIDs = $this->mongodb->selectCollection("ccp", "typeIDs");
        $shipIDs = array();
        $queryIDs = $typeIDs->find(array("groupID" => array("\$in" => array(547, 485, 513, 902, 941, 30, 659))), array("projection" => array("typeID" => 1, "_id" => 0)))->toArray();
        foreach($queryIDs as $t)
            $shipIDs[] = $t["typeID"];

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("victim.shipTypeID" => array("\$in" => $shipIDs)), $limit, 30, "DESC", $offset, $extras);
    }

    public function getWSpace($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $typeIDs = $this->mongodb->selectCollection("ccp", "solarSystems");
        $solarSystemIDs = array();
        $queryIDs = $typeIDs->find(array("regionID" => array("\$gte" => 11000001, "\$lte" => 11000033)), array("projection" => array("solarSystemID" => 1, "_id" => 0)))->toArray();
        foreach($queryIDs as $t)
            $solarSystemIDs[] = $t["solarSystemID"];

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("solarSystemID" => array("\$in" => $solarSystemIDs)), $limit, 30, "DESC", $offset, $extras);
    }

    public function getHighSec($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $typeIDs = $this->mongodb->selectCollection("ccp", "solarSystems");
        $solarSystemIDs = array();
        $queryIDs = $typeIDs->find(array("security" => array("\$gte" => 0.45)), array("projection" => array("solarSystemID" => 1, "_id" => 0)))->toArray();
        foreach($queryIDs as $t)
            $solarSystemIDs[] = $t["solarSystemID"];

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("solarSystemID" => array("\$in" => $solarSystemIDs)), $limit, 30, "DESC", $offset, $extras);
    }

    public function getLowSec($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $typeIDs = $this->mongodb->selectCollection("ccp", "solarSystems");
        $solarSystemIDs = array();
        $queryIDs = $typeIDs->find(array("security" => array("\$gte" => 0, "\$lte" => 0.45)), array("projection" => array("solarSystemID" => 1, "_id" => 0)))->toArray();
        foreach($queryIDs as $t)
            $solarSystemIDs[] = $t["solarSystemID"];

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("solarSystemID" => array("\$in" => $solarSystemIDs)), $limit, 30, "DESC", $offset, $extras);
    }

    public function getNullSec($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $typeIDs = $this->mongodb->selectCollection("ccp", "solarSystems");
        $solarSystemIDs = array();
        $queryIDs = $typeIDs->find(array("security" => array("\$lte" => 0), "wormholeClassID" => array("\$exists" => false)), array("projection" => array("solarSystemID" => 1, "_id" => 0)))->toArray();
        foreach($queryIDs as $t)
            $solarSystemIDs[] = $t["solarSystemID"];

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("solarSystemID" => array("\$in" => $solarSystemIDs)), $limit, 30, "DESC", $offset, $extras);
    }

    public function getSolo($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("isSolo" => true), $limit, 30, "DESC", $offset, $extras);
    }

    public function getNPC($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("isNPC" => true), $limit, 30, "DESC", $offset, $extras);
    }

    public function get5b($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("totalValue" => array("\$gte" => 5000000000)), $limit, 30, "DESC", $offset, $extras);
    }

    public function get10b($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("totalValue" => array("\$gte" => 10000000000)), $limit, 30, "DESC", $offset, $extras);
    }

    public function getCitadels($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $typeIDs = $this->mongodb->selectCollection("ccp", "typeIDs");
        $shipIDs = array();
        $queryIDs = $typeIDs->find(array("groupID" => array("\$in" => array(1657))), array("projection" => array("typeID" => 1, "_id" => 0)))->toArray();
        foreach($queryIDs as $t)
            $shipIDs[] = $t["typeID"];

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("victim.shipTypeID" => array("\$in" => $shipIDs)), $limit, 30, "DESC", $offset, $extras);
    }

    public function getT1($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $typeIDs = $this->mongodb->selectCollection("ccp", "typeIDs");
        $shipIDs = array();
        $queryIDs = $typeIDs->find(array("groupID" => array("\$in" => array(419,27,29,547,26,420,25,28,941,463,237,31))), array("projection" => array("typeID" => 1, "_id" => 0)))->toArray();
        foreach($queryIDs as $t)
            $shipIDs[] = $t["typeID"];

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("victim.shipTypeID" => array("\$in" => $shipIDs)), $limit, 30, "DESC", $offset, $extras);
    }

    public function getT2($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $typeIDs = $this->mongodb->selectCollection("ccp", "typeIDs");
        $shipIDs = array();
        $queryIDs = $typeIDs->find(array("groupID" => array("\$in" => array(324,898,906,540,830,893,543,541,833,358,894,831,902,832,900,834,380))), array("projection" => array("typeID" => 1, "_id" => 0)))->toArray();
        foreach($queryIDs as $t)
            $shipIDs[] = $t["typeID"];

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("victim.shipTypeID" => array("\$in" => $shipIDs)), $limit, 30, "DESC", $offset, $extras);
    }

    public function getT3($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $typeIDs = $this->mongodb->selectCollection("ccp", "typeIDs");
        $shipIDs = array();
        $queryIDs = $typeIDs->find(array("groupID" => array("\$in" => array(963, 1305))), array("projection" => array("typeID" => 1, "_id" => 0)))->toArray();
        foreach($queryIDs as $t)
            $shipIDs[] = $t["typeID"];

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("victim.shipTypeID" => array("\$in" => $shipIDs)), $limit, 30, "DESC", $offset, $extras);
    }

    public function getFrigates($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $typeIDs = $this->mongodb->selectCollection("ccp", "typeIDs");
        $shipIDs = array();
        $queryIDs = $typeIDs->find(array("groupID" => array("\$in" => array(324,893,25,831,237))), array("projection" => array("typeID" => 1, "_id" => 0)))->toArray();
        foreach($queryIDs as $t)
            $shipIDs[] = $t["typeID"];

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("victim.shipTypeID" => array("\$in" => $shipIDs)), $limit, 30, "DESC", $offset, $extras);
    }

    public function getDestroyers($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $typeIDs = $this->mongodb->selectCollection("ccp", "typeIDs");
        $shipIDs = array();
        $queryIDs = $typeIDs->find(array("groupID" => array("\$in" => array(420,541))), array("projection" => array("typeID" => 1, "_id" => 0)))->toArray();
        foreach($queryIDs as $t)
            $shipIDs[] = $t["typeID"];

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("victim.shipTypeID" => array("\$in" => $shipIDs)), $limit, 30, "DESC", $offset, $extras);
    }

    public function getCruisers($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $typeIDs = $this->mongodb->selectCollection("ccp", "typeIDs");
        $shipIDs = array();
        $queryIDs = $typeIDs->find(array("groupID" => array("\$in" => array(906,26,833,358,894,832,963))), array("projection" => array("typeID" => 1, "_id" => 0)))->toArray();
        foreach($queryIDs as $t)
            $shipIDs[] = $t["typeID"];

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("victim.shipTypeID" => array("\$in" => $shipIDs)), $limit, 30, "DESC", $offset, $extras);
    }

    public function getBattleCruisers($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $typeIDs = $this->mongodb->selectCollection("ccp", "typeIDs");
        $shipIDs = array();
        $queryIDs = $typeIDs->find(array("groupID" => array("\$in" => array(419,540))), array("projection" => array("typeID" => 1, "_id" => 0)))->toArray();
        foreach($queryIDs as $t)
            $shipIDs[] = $t["typeID"];

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("victim.shipTypeID" => array("\$in" => $shipIDs)), $limit, 30, "DESC", $offset, $extras);
    }

    public function getBattleShips($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $typeIDs = $this->mongodb->selectCollection("ccp", "typeIDs");
        $shipIDs = array();
        $queryIDs = $typeIDs->find(array("groupID" => array("\$in" => array(27,898,900))), array("projection" => array("typeID" => 1, "_id" => 0)))->toArray();
        foreach($queryIDs as $t)
            $shipIDs[] = $t["typeID"];

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("victim.shipTypeID" => array("\$in" => $shipIDs)), $limit, 30, "DESC", $offset, $extras);
    }

    public function getCapitals($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $typeIDs = $this->mongodb->selectCollection("ccp", "typeIDs");
        $shipIDs = array();
        $queryIDs = $typeIDs->find(array("groupID" => array("\$in" => array(547,485))), array("projection" => array("typeID" => 1, "_id" => 0)))->toArray();
        foreach($queryIDs as $t)
            $shipIDs[] = $t["typeID"];

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("victim.shipTypeID" => array("\$in" => $shipIDs)), $limit, 30, "DESC", $offset, $extras);
    }

    public function getFreighters($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $typeIDs = $this->mongodb->selectCollection("ccp", "typeIDs");
        $shipIDs = array();
        $queryIDs = $typeIDs->find(array("groupID" => array("\$in" => array(513,902,941))), array("projection" => array("typeID" => 1, "_id" => 0)))->toArray();
        foreach($queryIDs as $t)
            $shipIDs[] = $t["typeID"];

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("victim.shipTypeID" => array("\$in" => $shipIDs)), $limit, 30, "DESC", $offset, $extras);
    }

    public function getSuperCarriers($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $typeIDs = $this->mongodb->selectCollection("ccp", "typeIDs");
        $shipIDs = array();
        $queryIDs = $typeIDs->find(array("groupID" => array("\$in" => array(659))), array("projection" => array("typeID" => 1, "_id" => 0)))->toArray();
        foreach($queryIDs as $t)
            $shipIDs[] = $t["typeID"];

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("victim.shipTypeID" => array("\$in" => $shipIDs)), $limit, 30, "DESC", $offset, $extras);
    }

    public function getTitans($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        $typeIDs = $this->mongodb->selectCollection("ccp", "typeIDs");
        $shipIDs = array();
        $queryIDs = $typeIDs->find(array("groupID" => array("\$in" => array(30))), array("projection" => array("typeID" => 1, "_id" => 0)))->toArray();
        foreach($queryIDs as $t)
            $shipIDs[] = $t["typeID"];

        $extras = array("projection" => array("_id" => 0, "items" => 0, "osmium" => 0));
        return $this->getAllKills(array("victim.shipTypeID" => array("\$in" => $shipIDs)), $limit, 30, "DESC", $offset, $extras);
    }
}