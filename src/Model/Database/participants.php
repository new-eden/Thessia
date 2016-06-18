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

namespace Thessia\Model\Database;

use DateTime;
use Thessia\Helper\Mongo;

/**
 */
class participants extends Mongo
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
    
    public function getByKillID($killID, $cacheTime = 3600) {
        // Check if the killmail is in the cache, if it is, return it
        $killData = $this->cache->get($killID);
        if(!empty($killData))
            return $killData;

        // The killmail was not in the cache, time to get it from the db
        $killData = $this->collection->findOne(
            array("killID" => $killID)
        );

        // Store the killData in the cache
        $this->cache->set($killID, $killData, $cacheTime);

        // Return the killmail
        return $killData;
    }

    /**
     * Generates the queryArray AND validates the input from the extraArguments !
     *
     * @param array $extraArguments
     * @param int $limit
     * @param string $order
     * @param null $offset
     * @param string $groupBy
     */
    private function generateQueryArray($extraArguments = array(), $limit = 100, $order = "DESC", $offset = null, $groupBy = "killID") {
        // Map arguments to mongo query fields
        // Example: $extraArguments = array("finalBlow" => 1); should map to: array("attackers.finalBlow" => 1);
        // And also validate them at the same time (int, float, string etc.)
    }
    
    public function verifyDate($date): bool {
        return (DateTime::createFromFormat("Y-m-d H:i:s", $date) !== false);
    }
    
    public function getByKillTime($killTime, $extraArguments = array(), $limit = 100, $cacheTime = 3600, $order = "DESC", $offset = null, $groupBy = "killID") {
        
    }
    
    public function getBySolarSystemID() {
        
    }

    public function getByRegionID() {

    }

    public function getByCharacterID() {

    }

    public function getByCorporationID() {

    }

    public function getByAllianceID() {

    }

    public function getByFactionID() {

    }

    public function getByShipTypeID() {

    }

    public function getByGroupID() {

    }

    public function getByVGroupID() {

    }

    public function getByWeaponTypeID() {

    }

    public function getAllKillsAfterDate() {

    }

    public function getAllKillsBeforeDate() {

    }

    public function getAllKillsBetweenDates() {

    }

    public function getAllKills() {

    }
}