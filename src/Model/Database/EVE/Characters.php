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
 * Class Characters
 * @package Thessia\Model\Database\EVE
 */
class Characters extends Mongo
{
    /**
     * The name of the models collection
     */
    public $collectionName = 'characters';

    /**
     * The name of the database the collection is stored in
     */
    public $databaseName = 'thessia';

    /**
     * An array of indexes for this collection
     */
    public $indexes = array(
        array(
            "key" => array("characterID" => -1),
            "unique" => true
        ),
        array(
            "key" => array("characterName" => "text")
        ),
        array(
            "key" => array("characterName" => -1)
        ),
        array(
            "key" => array("corporationID" => -1)
        ),
        array(
            "key" => array("corporationName" => -1)
        ),
        array(
            "key" => array("allianceID" => -1)
        ),
        array(
            "key" => array("allianceName" => -1)
        ),
        array(
            "key" => array("lastUpdated" => -1)
        )
    );

    /**
     * @param int $characterID
     * @return array|null|object
     */
    public function getAllByID(int $characterID)
    {
        return $this->collection->findOne(array("characterID" => $characterID));
    }

    /**
     * @param string $characterName
     * @return array|null|object
     */
    public function getAllByName(string $characterName)
    {
        return $this->collection->findOne(array("characterName" => $characterName));
    }

    public function getCharacterCount() {
        return $this->collection->count();
    }

    public function getCharacterInformation(int $characterID): array {
        return $this->collection->findOne(array("characterID" => $characterID), array("projection" => array("_id" => 0)));
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
     * @param array $filter
     * @param array $update
     * @param array $options
     * @return \MongoDB\UpdateResult|string
     */
    public function updateOne(array $filter, array $update, array $options = []) {
        try {
            return $this->collection->updateOne($filter, $update, $options);
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
    public function replaceOne(array $filter, array $replacement, array $options = [])
    {
        try {
            return $this->collection->replaceOne($filter, $replacement, $options);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}