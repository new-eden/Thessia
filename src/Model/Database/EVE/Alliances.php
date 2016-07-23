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
 * Class Alliances
 * @package Thessia\Model\Database\EVE
 */
class Alliances extends Mongo
{
    /**
     * The name of the models collection
     */
    public $collectionName = 'alliances';

    /**
     * The name of the database the collection is stored in
     */
    public $databaseName = 'thessia';

    /**
     * An array of indexes for this collection
     */
    public $indexes = array(
        array(
            "key" => array("allianceID" => -1),
            "unique" => true
        ),
        array(
            "key" => array("allianceName" => "text")
        ),
        array(
            "key" => array("allianceName" => -1)
        ),
        array(
            "key" => array("ticker" => -1)
        )
    );

    /**
     * @param int $allianceID
     * @return array|null|object
     */
    public function getAllByID(int $allianceID)
    {
        return $this->collection->findOne(array("allianceID" => $allianceID));
    }

    /**
     * @param string $allianceName
     * @return array|null|object
     */
    public function getAllByName(string $allianceName)
    {
        return $this->collection->findOne(array("allianceName" => $allianceName));
    }

    public function getAllianceCount() {
        return $this->collection->count();
    }

    public function getAllianceInformation(int $allianceID): array {
        return $this->collection->find(array("allianceID" => $allianceID))->toArray();
    }

    public function getAllianceMembers(int $allianceID): array {
        $characters = $this->mongodb->selectCollection("thessia", "characters");
        return $characters->find(array("allianceID" => $allianceID))->toArray();
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