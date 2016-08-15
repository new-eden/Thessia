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

use Thessia\Helper\Mongo;

/**
 */
class Killmails extends Mongo
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
    public $indexes = array(
        array(
            "key" => array("killID" => -1),
            "unique" => true
        ),
        array(
            "key" => array("killTime" => -1)
        ),
        array(
            "key" => array("solarSystemID" => -1)
        ),
        array(
            "key" => array("solarSystemName" => -1)
        ),
        array(
            "key" => array("regionID" => -1)
        ),
        array(
            "key" => array("regionName" => -1)
        ),
        array(
            "key" => array("victim.characterID" => -1)
        ),
        array(
            "key" => array("victim.characterName" => -1)
        ),
        array(
            "key" => array("victim.corporationID" => -1)
        ),
        array(
            "key" => array("victim.corporationName" => -1)
        ),
        array(
            "key" => array("victim.allianceID" => -1)
        ),
        array(
            "key" => array("victim.allianceName" => -1)
        ),
        array(
            "key" => array("victim.factionID" => -1)
        ),
        array(
            "key" => array("victim.factionName" => -1)
        ),
        array(
            "key" => array("victim.shipTypeID" => -1)
        ),
        array(
            "key" => array("victim.shipTypeName" => -1)
        ),
        array(
            "key" => array("victim.damageTaken" => -1)
        ),
        array(
            "key" => array("attackers.characterID" => -1)
        ),
        array(
            "key" => array("attackers.characterName" => -1)
        ),
        array(
            "key" => array("attackers.corporationID" => -1)
        ),
        array(
            "key" => array("attackers.corporationName" => -1)
        ),
        array(
            "key" => array("attackers.allianceID" => -1)
        ),
        array(
            "key" => array("attackers.allianceName" => -1)
        ),
        array(
            "key" => array("attackers.factionID" => -1)
        ),
        array(
            "key" => array("attackers.factionName" => -1)
        ),
        array(
            "key" => array("attackers.shipTypeID" => -1)
        ),
        array(
            "key" => array("attackers.shipTypeName" => -1)
        ),
        array(
            "key" => array("attackers.finalBlow" => -1)
        ),
        array(
            "key" => array("attackers.weaponTypeID" => -1)
        ),
        array(
            "key" => array("attackers.weaponTypeName" => -1)
        ),
        array(
            "key" => array("attackers.damageDone" => -1)
        ),
        array(
            "key" => array("items.typeID" => -1)
        ),
        array(
            "key" => array("items.typeName" => -1)
        ),
        array(
            "key" => array("items.groupID" => -1)
        )
    );

    /**
     * @param $crestHash
     * @return \MongoDB\Driver\Cursor
     */
    public function getAllByCrestHash($crestHash)
    {
        return $this->collection->find(
            array("crestHash" => $crestHash)
        );
    }

    /**
     * @param $killID
     * @return \MongoDB\Driver\Cursor
     */
    public function getAllByKillID($killID)
    {
        return $this->collection->find(
            array("killID" => $killID)
        );
    }

    /**
     * @param $moonID
     * @return \MongoDB\Driver\Cursor
     */
    public function getAllByMoonID($moonID)
    {
        return $this->collection->find(
            array("moonID" => $moonID)
        );
    }

    /**
     * @param $regionID
     * @return \MongoDB\Driver\Cursor
     */
    public function getAllByRegionID($regionID)
    {
        return $this->collection->find(
            array("regionID" => $regionID)
        );
    }

    /**
     * @param $fieldName
     * @return \MongoDB\Driver\Cursor
     */
    public function getAllByRegionName($fieldName)
    {
        return $this->collection->find(
            array("regionName" => $fieldName)
        );
    }

    /**
     * @param $solarSystemID
     * @return \MongoDB\Driver\Cursor
     */
    public function getAllBySolarSystemID($solarSystemID)
    {
        return $this->collection->find(
            array("solarSystemID" => $solarSystemID)
        );
    }

    /**
     * @param $fieldName
     * @return \MongoDB\Driver\Cursor
     */
    public function getAllBySolarSystemName($fieldName)
    {
        return $this->collection->find(
            array("solarSystemName" => $fieldName)
        );
    }

    /**
     * @param $victimAllianceID
     * @return \MongoDB\Driver\Cursor
     */
    public function getAllByVictimAllianceID($victimAllianceID)
    {
        return $this->collection->find(
            array("victim.allianceID" => $victimAllianceID)
        );
    }

    /**
     * @param $fieldName
     * @return \MongoDB\Driver\Cursor
     */
    public function getAllByVictimAllianceName($fieldName)
    {
        return $this->collection->find(
            array("victim.allianceName" => $fieldName)
        );
    }

    /**
     * @param $victimCharacterID
     * @return \MongoDB\Driver\Cursor
     */
    public function getAllByVictimCharacterID($victimCharacterID)
    {
        return $this->collection->find(
            array("victim.characterID" => $victimCharacterID)
        );
    }

    /**
     * @param $fieldName
     * @return \MongoDB\Driver\Cursor
     */
    public function getAllByVictimCharacterName($fieldName)
    {
        return $this->collection->find(
            array("victim.characterName" => $fieldName)
        );
    }

    /**
     * @param $victimCorporationID
     * @return \MongoDB\Driver\Cursor
     */
    public function getAllByVictimCorporationID($victimCorporationID)
    {
        return $this->collection->find(
            array("victim.corporationID" => $victimCorporationID)
        );
    }

    /**
     * @param $fieldName
     * @return \MongoDB\Driver\Cursor
     */
    public function getAllByVictimCorporationName($fieldName)
    {
        return $this->collection->find(
            array("victim.corporationName" => $fieldName)
        );
    }

    /**
     * @param $victimFactionID
     * @return \MongoDB\Driver\Cursor
     */
    public function getAllByVictimFactionID($victimFactionID)
    {
        return $this->collection->find(
            array("victim.factionID" => $victimFactionID)
        );
    }

    /**
     * @param $fieldName
     * @return \MongoDB\Driver\Cursor
     */
    public function getAllByVictimFactionName($fieldName)
    {
        return $this->collection->find(
            array("victim.factionName" => $fieldName)
        );
    }

    /**
     * @param $victimShipTypeID
     * @return \MongoDB\Driver\Cursor
     */
    public function getAllByVictimShipTypeID($victimShipTypeID)
    {
        return $this->collection->find(
            array("victim.shipTypeID" => $victimShipTypeID)
        );
    }

    /**
     * @param $fieldName
     * @return \MongoDB\Driver\Cursor
     */
    public function getAllByVictimShipTypeName($fieldName)
    {
        return $this->collection->find(
            array("victim.shipTypeName" => $fieldName)
        );
    }

    public function getKillmailCount() {
        return $this->collection->count();
    }

    /**
     * @param array $documents An array of arrays. eg: array(array(data), array(data2), array(data3))
     * @param array $options Options array, used for projection, sort, etc.
     * @return \MongoDB\InsertManyResult|string
     */
    public function insertMany(array $documents, array $options = [])
    {
        try {
            return $this->collection->insertMany($documents, $options);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param array $document The data array to insert.
     * @param array $options Options array, used for projection, sort, etc.
     * @return \MongoDB\InsertOneResult|string
     */
    public function insertOne(array $document, array $options = [])
    {
        try {
            return $this->collection->insertOne($document, $options);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param array $filter A Filter query, eg: array("killID" => 1).
     * @param array $replacement The new data array to replace the old one with.
     * @param array $options Options array, used for projection, sort, etc.
     * @return \MongoDB\UpdateResult|string
     */
    public function updateOne(array $filter, array $replacement, array $options = [])
    {
        try {
            return $this->collection->replaceOne($filter, $replacement, $options);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
