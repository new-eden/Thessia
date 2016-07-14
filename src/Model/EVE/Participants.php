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

namespace Thessia\Model\EVE;

use DateTime;
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

    /**
     * @param int $killID
     * @param int $cacheTime
     * @return array
     */
    public function getByKillID(int $killID, int $cacheTime = 360): array
    {
        // Check if the killmail is in the cache, if it is, return it
        $killData = $this->cache->get(md5(serialize($killID)));
        if (!empty($killData)) {
            return $killData;
        }

        // The killmail was not in the cache, time to get it from the db
        $killData = $this->collection->findOne(
            array("killID" => $killID)
        );

        // Store the killData in the cache
        $this->cache->set(md5(serialize($killID)), $killData, $cacheTime);

        // Return the killmail
        return $killData;
    }

    /**
     * @param string $crestHash
     * @param int $cacheTime
     * @return array
     */
    public function getByCrestHash(string $crestHash, int $cacheTime = 360): array
    {
        // Check if the killmail is in the cache, if it is, return it
        $killData = $this->cache->get(md5(serialize($crestHash)));
        if (!empty($killData)) {
            return $killData;
        }

        // The killmail was not in the cache, time to get it from the db
        $killData = $this->collection->findOne(
            array("crestHash" => $crestHash)
        );

        // Store the killData in the cache
        $this->cache->set(md5(serialize($crestHash)), $killData, $cacheTime);

        // Return the killmail
        return $killData;
    }

    /**
     * @param $killTime
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed|null|object
     * @throws \Exception
     */
    public function getByKillTime($killTime, $extraArguments = array(), $limit = 100, $cacheTime = 360, $order = "DESC", $offset = null)
    {
        $killData = $this->cache->get(md5(serialize($killTime)));
        if (!empty($killData)) {
            return $killData;
        }

        $extraArguments["killTime"] = new \MongoDB\BSON\UTCDatetime(strtotime($killTime));
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->findOne($array);
        $this->cache->set(md5(serialize($killTime)), $killData, $cacheTime);
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
    private function generateQueryArray($extraArguments = array(), int $limit = 100, string $order = "DESC", int $offset = null): array
    {
        // Mongo Array
        $queryArray = array();
        $dataArray = array();

        // Valid arguments
        $validArguments = array(
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

        // If there are extraArguments, we'll run through them, and validate each one (it's all numeric values tho, except killtime, pretty easy to validate)
        if (!empty($extraArguments)) {
            // Now validate everything from extraArguments
            foreach ($validArguments as $arg) {
                if (isset($extraArguments[$arg])) {
                    // Do some checks, to make sure it's all numeric values or in the case of the killTime, a valid timestamp
                    if ($arg == "killTime") {
                        if ($this->verifyDate($extraArguments[$arg]) == false) {
                            throw new \Exception("Error, {$arg} is not a valid Y-m-d H:i:s timestamp");
                        }
                    } else {
                        if (!is_numeric($extraArguments[$arg])) {
                            throw new \Exception("Error, {$arg} is not a numeric value");
                        }
                    }

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
    public function getBySolarSystemID($solarSystemID, $extraArguments = array(), $limit = 100, $cacheTime = 360, $order = "DESC", $offset = null)
    {
        $killData = $this->cache->get(md5(serialize($solarSystemID)));
        if (!empty($killData)) {
            return $killData;
        }

        $extraArguments["solarSystemID"] = $solarSystemID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        $this->cache->set(md5(serialize($solarSystemID)), $killData, $cacheTime);
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
    public function getByRegionID($regionID, $extraArguments = array(), $limit = 100, $cacheTime = 360, $order = "DESC", $offset = null)
    {
        $killData = $this->cache->get(md5(serialize($regionID)));
        if (!empty($killData)) {
            return $killData;
        }

        $extraArguments["regionID"] = $regionID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        $this->cache->set(md5(serialize($regionID)), $killData, $cacheTime);
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
    public function getByVictimCharacterID($characterID, $extraArguments = array(), $limit = 100, $cacheTime = 360, $order = "DESC", $offset = null)
    {
        $killData = $this->cache->get(md5(serialize($characterID)));
        if (!empty($killData)) {
            return $killData;
        }

        $extraArguments["victim.characterID"] = $characterID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        $this->cache->set(md5(serialize($characterID)), $killData, $cacheTime);
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
    public function getByAttackerCharacterID($characterID, $extraArguments = array(), $limit = 100, $cacheTime = 360, $order = "DESC", $offset = null)
    {
        $killData = $this->cache->get(md5(serialize($characterID)));
        if (!empty($killData)) {
            return $killData;
        }

        $extraArguments["attackers.characterID"] = $characterID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        $this->cache->set(md5(serialize($characterID)), $killData, $cacheTime);
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
    public function getByVictimCorporationID($corporationID, $extraArguments = array(), $limit = 100, $cacheTime = 360, $order = "DESC", $offset = null)
    {
        $killData = $this->cache->get(md5(serialize($corporationID)));
        if (!empty($killData)) {
            return $killData;
        }

        $extraArguments["victim.corporationID"] = $corporationID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        $this->cache->set(md5(serialize($corporationID)), $killData, $cacheTime);
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
    public function getByAttackerCorporationID($corporationID, $extraArguments = array(), $limit = 100, $cacheTime = 360, $order = "DESC", $offset = null)
    {
        $killData = $this->cache->get(md5(serialize($corporationID)));
        if (!empty($killData)) {
            return $killData;
        }

        $extraArguments["attackers.corporationID"] = $corporationID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        $this->cache->set(md5(serialize($corporationID)), $killData, $cacheTime);
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
    public function getByVictimAllianceID($allianceID, $extraArguments = array(), $limit = 100, $cacheTime = 360, $order = "DESC", $offset = null)
    {
        $killData = $this->cache->get(md5(serialize($allianceID)));
        if (!empty($killData)) {
            return $killData;
        }

        $extraArguments["victim.allianceID"] = $allianceID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        $this->cache->set(md5(serialize($allianceID)), $killData, $cacheTime);
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
    public function getByAttackerAllianceID($allianceID, $extraArguments = array(), $limit = 100, $cacheTime = 360, $order = "DESC", $offset = null)
    {
        $killData = $this->cache->get(md5(serialize($allianceID)));
        if (!empty($killData)) {
            return $killData;
        }

        $extraArguments["attackers.allianceID"] = $allianceID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        $this->cache->set(md5(serialize($allianceID)), $killData, $cacheTime);
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
    public function getByVictimFactionID($factionID, $extraArguments = array(), $limit = 100, $cacheTime = 360, $order = "DESC", $offset = null)
    {
        $killData = $this->cache->get(md5(serialize($factionID)));
        if (!empty($killData)) {
            return $killData;
        }

        $extraArguments["victim.factionID"] = $factionID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        $this->cache->set(md5(serialize($factionID)), $killData, $cacheTime);
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
    public function getByAttackerFactionID($factionID, $extraArguments = array(), $limit = 100, $cacheTime = 360, $order = "DESC", $offset = null)
    {
        $killData = $this->cache->get(md5(serialize($factionID)));
        if (!empty($killData)) {
            return $killData;
        }

        $extraArguments["attackers.factionID"] = $factionID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        $this->cache->set(md5(serialize($factionID)), $killData, $cacheTime);
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
    public function getByVictimShipTypeID($shipTypeID, $extraArguments = array(), $limit = 100, $cacheTime = 360, $order = "DESC", $offset = null)
    {
        $killData = $this->cache->get(md5(serialize($shipTypeID)));
        if (!empty($killData)) {
            return $killData;
        }

        $extraArguments["victim.shipTypeID"] = $shipTypeID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        $this->cache->set(md5(serialize($shipTypeID)), $killData, $cacheTime);
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
    public function getByAttackerShipTypeID($shipTypeID, $extraArguments = array(), $limit = 100, $cacheTime = 360, $order = "DESC", $offset = null)
    {
        $killData = $this->cache->get(md5(serialize($shipTypeID)));
        if (!empty($killData)) {
            return $killData;
        }

        $extraArguments["attackers.shipTypeID"] = $shipTypeID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        $this->cache->set(md5(serialize($shipTypeID)), $killData, $cacheTime);
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
    public function getByAttackerWeaponTypeID($weaponTypeID, $extraArguments = array(), $limit = 100, $cacheTime = 360, $order = "DESC", $offset = null)
    {
        $killData = $this->cache->get(md5(serialize($weaponTypeID)));
        if (!empty($killData)) {
            return $killData;
        }

        $extraArguments["attackers.weaponTypeID"] = $weaponTypeID;
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        $this->cache->set(md5(serialize($weaponTypeID)), $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param null $afterDate
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getAllKillsAfterDate($afterDate = null, $extraArguments = array(), $limit = 100, $cacheTime = 360, $order = "DESC", $offset = null)
    {
        $killData = $this->cache->get(md5(serialize($afterDate)));
        if (!empty($killData)) {
            return $killData;
        }

        $extraArguments["killTime"] = array("\$gte" => new \MongoDB\BSON\UTCDatetime(strtotime($afterDate)));
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        $this->cache->set(md5(serialize($afterDate)), $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param null $beforeDate
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getAllKillsBeforeDate($beforeDate = null, $extraArguments = array(), $limit = 100, $cacheTime = 360, $order = "DESC", $offset = null)
    {
        $killData = $this->cache->get(md5(serialize($beforeDate)));
        if (!empty($killData)) {
            return $killData;
        }

        $extraArguments["killTime"] = array("\$lte" => new \MongoDB\BSON\UTCDatetime(strtotime($beforeDate)));
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        $this->cache->set(md5(serialize($beforeDate)), $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param null $afterDate
     * @param null $beforeDate
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getAllKillsBetweenDates($afterDate = null, $beforeDate = null, $extraArguments = array(), $limit = 100, $cacheTime = 360, $order = "DESC", $offset = null)
    {
        $killData = $this->cache->get(md5(serialize($afterDate . $beforeDate)));
        if (!empty($killData)) {
            return $killData;
        }

        $extraArguments["killTime"] = array("\$gte" => new \MongoDB\BSON\UTCDatetime(strtotime($afterDate)), "\$lte" => new \MongoDB\BSON\UTCDatetime(strtotime($beforeDate)));
        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        $this->cache->set(md5(serialize($afterDate . $beforeDate)), $killData, $cacheTime);
        return $killData;
    }

    /**
     * @param array $extraArguments
     * @param int $limit
     * @param int $cacheTime
     * @param string $order
     * @param null $offset
     * @return array|mixed
     * @throws \Exception
     */
    public function getAllKills($extraArguments = array(), $limit = 100, $cacheTime = 360, $order = "DESC", $offset = null)
    {
        $killData = $this->cache->get(md5(serialize($extraArguments)));
        if (!empty($killData)) {
            return $killData;
        }

        $array = $this->generateQueryArray($extraArguments, $limit, $order, $offset);
        $killData = $this->collection->find($array["filter"], $array["options"])->toArray();
        $this->cache->set(md5(serialize($extraArguments)), $killData, $cacheTime);
        return $killData;
    }
}