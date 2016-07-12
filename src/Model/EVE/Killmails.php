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
    public $indexes = array();

    /**
     * @param mixed $crestHash
     */
    public function getAllByCrestHash($crestHash)
    {
        return $this->collection->find(
            array("crestHash" => $crestHash)
        );
    }

    /**
     * @param mixed $killID
     */
    public function getAllByKillID($killID)
    {
        return $this->collection->find(
            array("killID" => $killID)
        );
    }

    /**
     * @param mixed $moonID
     */
    public function getAllByMoonID($moonID)
    {
        return $this->collection->find(
            array("moonID" => $moonID)
        );
    }

    /**
     * @param mixed $regionID
     */
    public function getAllByRegionID($regionID)
    {
        return $this->collection->find(
            array("regionID" => $regionID)
        );
    }

    /**
     * @param mixed $fieldName
     */
    public function getAllByRegionName($fieldName)
    {
        return $this->collection->find(
            array("regionName" => $fieldName)
        );
    }

    /**
     * @param mixed $solarSystemID
     */
    public function getAllBySolarSystemID($solarSystemID)
    {
        return $this->collection->find(
            array("solarSystemID" => $solarSystemID)
        );
    }

    /**
     * @param mixed $fieldName
     */
    public function getAllBySolarSystemName($fieldName)
    {
        return $this->collection->find(
            array("solarSystemName" => $fieldName)
        );
    }

    /**
     * @param mixed $victimAllianceID
     */
    public function getAllByVictimAllianceID($victimAllianceID)
    {
        return $this->collection->find(
            array("victim.allianceID" => $victimAllianceID)
        );
    }

    /**
     * @param mixed $fieldName
     */
    public function getAllByVictimAllianceName($fieldName)
    {
        return $this->collection->find(
            array("victim.allianceName" => $fieldName)
        );
    }

    /**
     * @param mixed $victimCharacterID
     */
    public function getAllByVictimCharacterID($victimCharacterID)
    {
        return $this->collection->find(
            array("victim.characterID" => $victimCharacterID)
        );
    }

    /**
     * @param mixed $fieldName
     */
    public function getAllByVictimCharacterName($fieldName)
    {
        return $this->collection->find(
            array("victim.characterName" => $fieldName)
        );
    }

    /**
     * @param mixed $victimCorporationID
     */
    public function getAllByVictimCorporationID($victimCorporationID)
    {
        return $this->collection->find(
            array("victim.corporationID" => $victimCorporationID)
        );
    }

    /**
     * @param mixed $fieldName
     */
    public function getAllByVictimCorporationName($fieldName)
    {
        return $this->collection->find(
            array("victim.corporationName" => $fieldName)
        );
    }

    /**
     * @param mixed $victimFactionID
     */
    public function getAllByVictimFactionID($victimFactionID)
    {
        return $this->collection->find(
            array("victim.factionID" => $victimFactionID)
        );
    }

    /**
     * @param mixed $fieldName
     */
    public function getAllByVictimFactionName($fieldName)
    {
        return $this->collection->find(
            array("victim.factionName" => $fieldName)
        );
    }

    /**
     * @param mixed $victimShipTypeID
     */
    public function getAllByVictimShipTypeID($victimShipTypeID)
    {
        return $this->collection->find(
            array("victim.shipTypeID" => $victimShipTypeID)
        );
    }

    /**
     * @param mixed $fieldName
     */
    public function getAllByVictimShipTypeName($fieldName)
    {
        return $this->collection->find(
            array("victim.shipTypeName" => $fieldName)
        );
    }

    /**
     * @param array $documents An array of arrays. eg: array(array(data), array(data2), array(data3))
     * @param array $options Options array, used for projection, sort, etc.
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
