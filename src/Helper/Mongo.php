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

namespace Thessia\Helper;

// brain fuck: http://mongodb.github.io/mongo-php-library/tutorial/crud/
use MongoDB;
use MongoDB\Client;
use MongoDB\Collection;
use Thessia\Lib\Cache;
use Thessia\Lib\Config;

/**
 * Class Mongo
 * @package Thessia\Helper
 */
class Mongo {
    /**
     * @var Client
     */
    protected $mongodb;
    /**
     * @var Collection
     */
    protected $collection;
    /**
     * The name of the collection being used
     * @var string
     */
    public $collectionName = "";
    /**
     * The name of the database being used (Defaults to what is defined in the config)
     * @var string
     */
    public $databaseName = "";
    /**
     * @var array
     */
    public $indexes = array();

    /**
     * @var Cache
     */
    public $cache;

    /**
     * Mongo constructor.
     * @param Config $config
     * @param Client $mongodb
     * @param Cache $cache
     */
    public function __construct(Config $config, Client $mongodb, Cache $cache) {
        $this->cache = $cache;
        $this->mongodb = $mongodb;
        $db = !empty($this->databaseName) ? $this->databaseName : $config->get("dbName", "mongodb");
        $this->collection = $mongodb->selectCollection($db, $this->collectionName);
    }

    /**
     * Set $this->indexes with a list of indexes - Should only be called from CLI
     * @todo add output logging and whatnots
     */
    public function createIndex() {
        foreach ($this->indexes as $index) {
            if(isset($index["unique"])) {
                unset($index["unique"]);
                $this->collection->createIndex($index, array("unique" => 1));
            }
            elseif(isset($index["sparse"])) {
                unset($index["sparse"]);
                $this->collection->createIndex($index, array("sparse" => 1));
            } else {
                $this->collection->createIndex($index);
            }
        }
    }
}