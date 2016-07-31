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
use MongoDB\BSON\UTCDatetime;
use MongoDB\Collection;
use Monolog\Logger;

class UpdateCharacters
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
        $collection = $mongo->selectCollection("thessia", "characters");

        $log->addInfo("CRON: Updating Characters from the EVE API");
        $date = strtotime(date("Y-m-d H:i:s", strtotime("-1 week"))) * 1000;
        $charactersToUpdate = $collection->find(array("lastUpdated" => array("\$lt" => new UTCDatetime($date))), array("limit" => 850))->toArray();

        foreach($charactersToUpdate as $char) {
            if($char["characterID"] > 0)
                \Resque::enqueue("low", '\Thessia\Tasks\Resque\UpdateCharacter', array("characterID" => $char["characterID"]));
        }
    }

    /**
     * Defines how often the cronjob runs, every 1 second, every 60 seconds, every 86400 seconds, etc.
     */
    public static function getRunTimes()
    {
        return 60;
    }
}