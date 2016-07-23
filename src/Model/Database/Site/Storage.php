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

/**
 * Created by PhpStorm.
 * User: micha
 * Date: 21-07-2016
 * Time: 19:23
 */

namespace Thessia\Model\Database\Site;


use Thessia\Helper\Mongo;

/**
 * Class Settings
 * @package Thessia\Model\Database\Site
 */
class Storage extends Mongo
{
    /**
     * The name of the models collection
     */
    public $collectionName = 'storage';

    /**
     * The name of the database the collection is stored in
     */
    public $databaseName = 'thessia';

    /**
     * An array of indexes for this collection
     */
    public $indexes = array(
        array(
            "key" => array("key" => -1),
            "unique" => true
        )
    );

    /**
     * Get an object from the storage area
     *
     * @param string $key
     * @return array|null|object
     */
    public function get(string $key) {
        return $this->collection->findOne(array("key" => $key));
    }

    /**
     * Insert an object into the storage area
     *
     * @param string $key
     * @param $value Can be a string, null, array, object - the sky's the limit
     */
    public function set(string $key, $value) {
        $this->collection->replaceOne(array("key" => $key), array("key" => $key, "value" => $value));
    }
}