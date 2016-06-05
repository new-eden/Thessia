<?php

namespace Thessia\Helper;

// brain fuck: http://mongodb.github.io/mongo-php-library/tutorial/crud/
use MongoDB;
use MongoDB\Client;
use MongoDB\Collection;
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
     * @var string
     */
    public $collectionName = "";
    /**
     * @var array
     */
    public $indexes = array();

    /**
     * Mongo constructor.
     * @param Config $config
     * @param Client $mongodb
     */
    public function __construct(Config $config, Client $mongodb) {
        $this->mongodb = $mongodb;
        $this->collection = $mongodb->selectCollection($config->get("dbName", "mongodb"), $this->collectionName);
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
            } else {
                $this->collection->createIndex($index);
            }
        }
    }
}