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
use MongoDB\BSON\UTCDatetime;
use Thessia\Helper\Mongo;

/**
 */
class Participants extends Mongo
{

    /**
     * The name of the models collection
     */
    public $collectionName = 'killmails';

    /**
     * The name of the database the collection is stored in
     */
    public $databaseName = 'thessia';

    /**
     * An array of indexes for this collection
     */
    public $indexes = array();

    // Valid arguments
    public $validArguments = array(
        "killTime",
        "solarSystemID",
        "regionID",
        "shipValue",
        "fittingValue",
        "totalValue",
        "isNPC",
        "isSolo",
        "victim.shipTypeID",
        "victim.characterID",
        "victim.corporationID",
        "victim.allianceID",
        "victim.factionID",
        "attackers.shipTypeID",
        "attackers.weaponTypeID",
        "attackers.characterID",
        "attackers.corporationID",
        "attackers.allianceID",
        "attackers.factionID",
        "attackers.finalBlow",
        "items.typeID",
        "items.groupID",
        "items.categoryID",
    );

    /**
     * @param int $killID
     * @param int $cacheTime
     * @return array
     */
    public function getByKillID(int $killID, int $cacheTime = 300): array
    {
        // Check if the killmail is in the cache, if it is, return it
        $md5 = md5(serialize($killID));
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        // The killmail was not in the cache, time to get it from the db
        $killData = $this->collection->findOne(
            array("killID" => $killID)
        );
        $killData["killTime"] = date("Y-m-d H:i:s", (string)$killData["killTime"] / 1000);

        $this->cache->set($md5, $killData, $cacheTime);

        // Return the killmail
        return $killData;
    }

    /**
     * @param string $crestHash
     * @param int $cacheTime
     * @return array
     */
    public function getByCrestHash(string $crestHash, int $cacheTime = 300): array
    {
        // Check if the killmail is in the cache, if it is, return it
        $md5 = md5(serialize($crestHash));
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        // The killmail was not in the cache, time to get it from the db
        $killData = $this->collection->findOne(
            array("crestHash" => $crestHash)
        );
        $killData["killTime"] = date("Y-m-d H:i:s", (string)$killData["killTime"] / 1000);

        $this->cache->set($md5, $killData, $cacheTime);
        $this->cache->set(md5(serialize($crestHash)), $killData, $cacheTime);

        // Return the killmail
        return $killData;
    }

    /**
     * @param int|datetime $killTime
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed|null|object
     * @throws \Exception
     */
    public function getByKillTime($killTime, $extraArguments = array(), $limit = 100, $cacheTime = 300, $order = "DESC", $offset = null) {
        $md5 = md5(serialize($killTime . implode("", $extraArguments) . $limit . $order . $offset));
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        if(is_int($killTime))
            $time = $this->makeTimeFromUnixTime($killTime);
        else
            $time = $this->makeTimeFromDateTime($killTime);
        $extraArguments["killTime"] = $time;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->findOne($array);
        $killData["killTime"] = date("Y-m-d H:i:s", (string)$killData["killTime"] / 1000);
        $this->cache->set($md5, $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param array $extraArguments
     * @param int $limit
     * @param string $order
     * @param int|null $offset
     * @return array
     * @throws \Exception
     */
    private function generateQueryArray($extraArguments = array(), int $limit = 100, string $order = "DESC", int $offset = null ): array
    {
        // Mongo Array
        $queryArray = array();
        $dataArray = array();

        if (!empty($extraArguments)) {
            foreach ($this->validArguments as $arg) {
                if (isset($extraArguments[$arg])) {
                    $dataArray[$arg] = $extraArguments[$arg];
                }
            }
        }

        // Limit
        $queryArray["limit"] = $limit;

        // Order
        $queryArray["sort"] = array("killTime" => $order == "DESC" ? -1 : 1);

        // Offset
        if ($offset > 0) {
            $queryArray["skip"] = $offset;
        }

        // Remove _id
        $queryArray["projection"] = array("_id" => 0);

        // Return the query array
        return array("filter" => $dataArray, "options" => $queryArray);
    }

    /**
     * @param $date
     * @return bool
     */
    public function verifyDate($date): bool
    {
        return (DateTime::createFromFormat("Y-m-d H:i:s", $date) !== false);
    }

    /**
     * @param $solarSystemID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getBySolarSystemID($solarSystemID, $extraArguments = array(), $limit = 100, $cacheTime = 300, $order = "DESC", $offset = null) {
        $md5 = md5(serialize($solarSystemID . implode("", $extraArguments) . $limit . $order . $offset));
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        $extraArguments["solarSystemID"] = $solarSystemID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        foreach($killData as $key => $value)
            $killData[$key]["killTime"] = date(DateTime::ISO8601, $value["killTime"]->__toString() / 1000);

        $this->cache->set($md5, $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param $regionID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getByRegionID($regionID, $extraArguments = array(), $limit = 100, $cacheTime = 300, $order = "DESC", $offset = null) {
        $md5 = md5(serialize($regionID . implode("", $extraArguments) . $limit . $order . $offset));
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        $extraArguments["regionID"] = $regionID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();

        foreach($killData as $key => $value)
            $killData[$key]["killTime"] = date(DateTime::ISO8601, $value["killTime"]->__toString() / 1000);
        $this->cache->set($md5, $killData, $cacheTime);
        $this->cache->set(md5(serialize($regionID . implode("", $extraArguments) . $limit . $order . $offset)), $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param $characterID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getByVictimCharacterID($characterID, $extraArguments = array(), $limit = 100, $cacheTime = 300, $order = "DESC", $offset = null) {
        $md5 = md5(serialize("victim" . $characterID . implode("", $extraArguments) . $limit . $order . $offset));
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        $extraArguments["victim.characterID"] = $characterID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();

        foreach($killData as $key => $value)
            $killData[$key]["killTime"] = date(DateTime::ISO8601, $value["killTime"]->__toString() / 1000);

        $this->cache->set($md5, $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param $characterID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getByAttackerCharacterID($characterID, $extraArguments = array(), $limit = 100, $cacheTime = 300, $order = "DESC", $offset = null) {
        $md5 = md5(serialize($characterID . implode("", $extraArguments) . $limit . $order . $offset));
        if ($this->cache->exists($md5)) {
            return $this->cache->get($md5);
        }

        $extraArguments["attackers.characterID"] = $characterID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();

        foreach($killData as $key => $value)
            $killData[$key]["killTime"] = date(DateTime::ISO8601, $value["killTime"]->__toString() / 1000);

        $this->cache->set($md5, $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param $corporationID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getByVictimCorporationID($corporationID, $extraArguments = array(), $limit = 100, $cacheTime = 300, $order = "DESC", $offset = null) {
        $md5 = md5(serialize("victim" . $corporationID . implode("", $extraArguments) . $limit . $order . $offset));
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        $extraArguments["victim.corporationID"] = $corporationID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();

        foreach($killData as $key => $value)
            $killData[$key]["killTime"] = date(DateTime::ISO8601, $value["killTime"]->__toString() / 1000);

        $this->cache->set($md5, $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param $corporationID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getByAttackerCorporationID($corporationID, $extraArguments = array(), $limit = 100, $cacheTime = 300, $order = "DESC", $offset = null) {
        $md5 = md5(serialize($corporationID . implode("", $extraArguments) . $limit . $order . $offset));
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        $extraArguments["attackers.corporationID"] = $corporationID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();

        foreach($killData as $key => $value)
            $killData[$key]["killTime"] = date(DateTime::ISO8601, $value["killTime"]->__toString() / 1000);
        $this->cache->set($md5, $killData, $cacheTime);
        $this->cache->set(md5(serialize($corporationID . implode("", $extraArguments) . $limit . $order . $offset)), $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param $allianceID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getByVictimAllianceID($allianceID, $extraArguments = array(), $limit = 100, $cacheTime = 300, $order = "DESC", $offset = null) {
        $md5 = md5(serialize("victim" . $allianceID . implode("", $extraArguments) . $limit . $order . $offset));
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        $extraArguments["victim.allianceID"] = $allianceID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();

        foreach($killData as $key => $value)
            $killData[$key]["killTime"] = date(DateTime::ISO8601, $value["killTime"]->__toString() / 1000);

        $this->cache->set($md5, $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param $allianceID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getByAttackerAllianceID($allianceID, $extraArguments = array(), $limit = 100, $cacheTime = 300, $order = "DESC", $offset = null) {
        $md5 = md5(serialize($allianceID . implode("", $extraArguments) . $limit . $order . $offset));
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        $extraArguments["attackers.allianceID"] = $allianceID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);

        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();

        foreach($killData as $key => $value)
            $killData[$key]["killTime"] = date(DateTime::ISO8601, $value["killTime"]->__toString() / 1000);
        $this->cache->set($md5, $killData, $cacheTime);
        $this->cache->set(md5(serialize($allianceID . implode("", $extraArguments) . $limit . $order . $offset)), $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param $factionID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getByVictimFactionID($factionID, $extraArguments = array(), $limit = 100, $cacheTime = 300, $order = "DESC", $offset = null) {
        $md5 = md5(serialize("victim" . $factionID . implode("", $extraArguments) . $limit . $order . $offset));
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        $extraArguments["victim.factionID"] = $factionID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();

        foreach($killData as $key => $value)
            $killData[$key]["killTime"] = date(DateTime::ISO8601, $value["killTime"]->__toString() / 1000);

        $this->cache->set($md5, $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param $factionID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getByAttackerFactionID($factionID, $extraArguments = array(), $limit = 100, $cacheTime = 300, $order = "DESC", $offset = null) {
        $md5 = md5(serialize($factionID . implode("", $extraArguments) . $limit . $order . $offset));
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        $extraArguments["attackers.factionID"] = $factionID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();

        foreach($killData as $key => $value)
            $killData[$key]["killTime"] = date(DateTime::ISO8601, $value["killTime"]->__toString() / 1000);
        $this->cache->set($md5, $killData, $cacheTime);
        $this->cache->set(md5(serialize($factionID . implode("", $extraArguments) . $limit . $order . $offset)), $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param $shipTypeID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getByVictimShipTypeID($shipTypeID, $extraArguments = array(), $limit = 100, $cacheTime = 300, $order = "DESC", $offset = null) {
        $md5 = md5(serialize("victim" . $shipTypeID . implode("", $extraArguments) . $limit . $order . $offset));
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        $extraArguments["victim.shipTypeID"] = $shipTypeID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();

        foreach($killData as $key => $value)
            $killData[$key]["killTime"] = date(DateTime::ISO8601, $value["killTime"]->__toString() / 1000);

        $this->cache->set($md5, $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param $shipTypeID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getByAttackerShipTypeID($shipTypeID, $extraArguments = array(), $limit = 100, $cacheTime = 300, $order = "DESC", $offset = null) {
        $md5 = md5(serialize($shipTypeID . implode("", $extraArguments) . $limit . $order . $offset));
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        $extraArguments["attackers.shipTypeID"] = $shipTypeID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();

        foreach($killData as $key => $value)
            $killData[$key]["killTime"] = date(DateTime::ISO8601, $value["killTime"]->__toString() / 1000);
        $this->cache->set($md5, $killData, $cacheTime);
        $this->cache->set(md5(serialize($shipTypeID . implode("", $extraArguments) . $limit . $order . $offset)), $killData, $cacheTime);
        return $killData;
    }


    /**
     * @param $weaponTypeID
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getByAttackerWeaponTypeID($weaponTypeID, $extraArguments = array(), $limit = 100, $cacheTime = 300, $order = "DESC", $offset = null) {
        $md5 = md5(serialize($weaponTypeID . implode("", $extraArguments) . $limit . $order . $offset));
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        $extraArguments["attackers.weaponTypeID"] = $weaponTypeID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();

        foreach($killData as $key => $value)
            $killData[$key]["killTime"] = date(DateTime::ISO8601, $value["killTime"]->__toString() / 1000);
        $this->cache->set($md5, $killData, $cacheTime);
        $this->cache->set(md5(serialize($weaponTypeID . implode("", $extraArguments) . $limit . $order . $offset)), $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param int|datetime $afterDate
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getAllKillsAfterDate($afterDate = null, $extraArguments = array(), $limit = 100, $cacheTime = 300, $order = "DESC", $offset = null) {
        $md5 = md5(serialize($afterDate . implode("", $extraArguments) . $limit . $order . $offset));
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        if(is_int($afterDate))
            $time = $this->makeTimeFromUnixTime($afterDate);
        else
            $time = $this->makeTimeFromDateTime($afterDate);

        $extraArguments["killTime"] = array("\$gte" => $time);
        $this->cache->set($md5, $killData, $cacheTime);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();

        foreach($killData as $key => $value)
            $killData[$key]["killTime"] = date(DateTime::ISO8601, $value["killTime"]->__toString() / 1000);

        $this->cache->set(md5(serialize($afterDate . implode("", $extraArguments) . $limit . $order . $offset)), $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param int|datetime $beforeDate
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getAllKillsBeforeDate($beforeDate = null, $extraArguments = array(), $limit = 100, $cacheTime = 300, $order = "DESC", $offset = null) {
        $md5 = md5(serialize($beforeDate . implode("", $extraArguments) . $limit . $order . $offset));
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        if(is_int($beforeDate))
            $time = $this->makeTimeFromUnixTime($beforeDate);
        else
            $time = $this->makeTimeFromDateTime($beforeDate);

        $extraArguments["killTime"] = array("\$lte" => $time);
        $this->cache->set($md5, $killData, $cacheTime);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();

        foreach($killData as $key => $value)
            $killData[$key]["killTime"] = date(DateTime::ISO8601, $value["killTime"]->__toString() / 1000);

        $this->cache->set(md5(serialize($beforeDate . implode("", $extraArguments) . $limit . $order . $offset)), $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param int|datetime $afterDate
     * @param int|datetime $beforeDate
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getAllKillsBetweenDates($afterDate = null, $beforeDate = null, $extraArguments = array(), $limit = 100, $cacheTime = 300, $order = "DESC", $offset = null) {
        $md5 = md5(serialize($afterDate . $beforeDate . implode("", $extraArguments) . $limit . $order . $offset));
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        if(is_int($afterDate))
            $afterTime = $this->makeTimeFromUnixTime($afterDate);
        else
            $afterTime = $this->makeTimeFromDateTime($afterDate);
        if(is_int($beforeDate))
            $beforeTime = $this->makeTimeFromUnixTime($beforeDate);
        else
            $beforeTime = $this->makeTimeFromDateTime($beforeDate);
        $extraArguments["killTime"] = array(
            "\$gte" => $afterTime,
            "\$lte" => $beforeTime,
        );
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        foreach($killData as $key => $value)
            $killData[$key]["killTime"] = date(DateTime::ISO8601, $value["killTime"]->__toString() / 1000);

        $this->cache->set($md5, $killData, $cacheTime);
        return $killData;
    }
    /**
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @param array $extraOptions
     * @return array|mixed
     */
    public function getAllKills($extraArguments = array(), $limit = 100, $cacheTime = 300, $order = "DESC", $offset = null, array $extraOptions = array()) {
        $md5 = md5(serialize($extraArguments) . $limit . $order . $offset);
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $options = array_merge($array["options"], $extraOptions);
        
        // @todo rewrite the rest of Participants to use Aggregate - for some reason mongodb is a billion times faster
        // with aggregate (match + sort) than with find+sort.. don't ask me why.......

        // @todo make this isset add subtract bullshit into something nicer, maybe just if(isset(blabla)) $aggregateArray['$crap'] = $options["blabla"]; and then move on?!
        if(isset($options["skip"])) {
            $aggregateArray = array(
                array('$match' => $array["filter"]),
                array('$sort' => $options["sort"]),
                array('$skip' => $options["skip"]),
                array('$limit' => $options["limit"])
            );
        } else {
            $aggregateArray = array(
                array('$match' => $array["filter"]),
                array('$sort' => $options["sort"]),
                array('$limit' => $options["limit"])
            );
        }

        $killData = $this->collection->aggregate($aggregateArray)->toArray();

        // Remove things we have in our projection
        if(isset($options["projection"])) {
            foreach($killData as $key => $value) {
                foreach($options["projection"] as $name => $proj) {
                    unset($killData[$key][$name]);
                }

                // Fix the timestamp
                $killData[$key]["killTime"] = date(DateTime::ISO8601, $value["killTime"]->__toString() / 1000);
            }
        }

        $this->cache->set($md5, $killData, $cacheTime);
        return $killData;
    }
}