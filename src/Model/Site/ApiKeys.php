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


namespace Thessia\Model\Site;


use MongoDB\BSON\UTCDatetime;
use Thessia\Helper\Mongo;
use Thessia\Helper\Pheal;

/**
 * Class ApiKeys
 * @package Thessia\Model\Site
 */
class ApiKeys extends Mongo {
    /**
     * The name of the models collection
     */
    public $collectionName = 'apiKeys';

    /**
     * The name of the database the collection is stored in
     */
    public $databaseName = 'thessia';

    /**
     * An array of indexes for this collection
     */
    public $indexes = array(
        array(
            "key" => array("apiKey" => -1),
            "unique" => true
        ),
        array(
            "key" => array("belongsTo" => -1)
        ),
        array(
            "key" => array("lastValidation" => -1)
        )
    );

    /**
     * @param int $apiKey
     * @param string $vCode
     * @param int|null $userID
     * @param string|null $label
     */
    public function addKey(int $apiKey, string $vCode, int $userID = null, string $label = null) {
        $data = array(
            "apiKey" => $apiKey,
            "vCode" => $vCode,
            "belongsTo" => $userID,
            "label" => $label,
            "dateAdded" => $this->maketimeFromUnixTime(time()),
            "lastValidation" => null,
        );

        try {
            $this->collection->insertOne($data);
        } catch(\Exception $e) {
            $this->collection->updateOne(array("apiKey" => $apiKey), $data);
        }
    }

    /**
     * @param int $apiKey
     * @return array
     */
    public function getKey(int $apiKey): array {
        return $this->collection->find(array("apiKey" => $apiKey))->toArray();
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function updateKey(array $data) {
        if(isset($data["apiKey"])) {
            $apiKey = $data["apiKey"];
        }
        else {
            throw new \Exception("Error, no apiKey set. Make sure your data array contains the proper data.");
        }

        try {
            $this->collection->insertOne($data);
        } catch(\Exception $e) {
            $this->collection->updateOne(array("apiKey" => $apiKey), $data);
        }
    }

    /**
     * @param int $apiKey
     * @return int
     */
    public function deleteKey(int $apiKey): int {
        return $this->collection->deleteOne(array("apiKey" => $apiKey))->getDeletedCount();
    }
}