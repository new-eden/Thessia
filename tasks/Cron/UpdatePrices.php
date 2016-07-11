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

namespace Thessia\Tasks\Cron;

use League\Container\Container;
use MongoDB\Collection;
use Monolog\Logger;

class UpdatePrices
{
    /**
     * @param Container $container
     */
    public static function execute(Container $container)
    {
        /** @var \MongoClient $mongo */
        $mongo = $container->get("mongo");
        /** @var Logger $log */
        $log = $container->get("log");
        /** @var Collection $collection */
        $collection = $mongo->selectCollection("ccp", "typeIDs");
        /** @var Collection $priceCollection */
        $priceCollection = $mongo->selectCollection("thessia", "marketPrices");

        $log->addInfo("CRON: Updating item values from CREST");
        // Get the Market Prices from CREST
        $marketData = json_decode(file_get_contents("https://crest-tq.eveonline.com/market/prices/"), true);
        foreach($marketData["items"] as $data) {
            $typeID = $data["type"]["id"];
            $typeData = $collection->find(array("typeID" => $typeID))->toArray();

            if(empty($typeData[0]))
                continue;

            // If it's not empty, we bind typeData to typeData[0] to get the first element in the array..
            $typeData = $typeData[0];

            $priceArray = array(
                "typeID" => (int) $typeID,
                "typeNames" => $typeData["name"],
                "marketGroupID" => (int) isset($typeData["marketGroupID"]) ? $typeData["marketGroupID"] : 0,
                "groupID" => (int) $typeData["groupID"],
                "adjustedPrice" => (int) isset($data["adjustedPrice"]) ? $data["adjustedPrice"] : 0,
                "averagePrice" => (int) isset($data["averagePrice"]) ? $data["averagePrice"] : 0,
                "lastUpdated" => date("Y-m-d H:i:s")
            );
            $log->addInfo("CRON UpdatePrices: Updating {$typeData["name"]}");
            $priceCollection->replaceOne(array("typeID" => $typeID), $priceArray, array("upsert" => true));
        }
    }

    /**
     * Defines how often the cronjob runs, every 1 second, every 60 seconds, every 86400 seconds, etc.
     */
    public static function getRunTimes()
    {
        return 86400;
    }
}