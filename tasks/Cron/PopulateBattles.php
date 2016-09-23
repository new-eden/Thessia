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
use Thessia\Helper\CrestHelper;

class PopulateBattles {
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
        $collection = $mongo->selectCollection("thessia", "battles");
        $killmails = $mongo->selectCollection("thessia", "killmails");

        $startTime = strtotime(date("2007-12-05 20:00:00"));
        $endTime = time();

        // We run with a 2hour interval for finding battles
        $dateAfter = time() - 3600;
        $dateBefore = time() + 3600;
        $date1 = new UTCDatetime((time()-3600)*1000);
        $date2 = new UTCDatetime((time()+3600)*1000);

        // Make the aggregation pipeline
        $aggregation = array(
            array('$match' => array("killTime" => array('$gte' => $date1, '$lte' => $date2))),
            array('$group' => array('_id' => '$solarSystemID', 'count' => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "solarSystemID" => '$_id')),
            array('$match' => array('count' => array('$gte' => 50))),
            array('$sort' => array('count' => -1))
        );

        // Do the query
        $result = $killmails->aggregate($aggregation)->toArray();

        var_dump($result);

        exit;
    }

    /**
     * Defines how often the cronjob runs, every 1 second, every 60 seconds, every 86400 seconds, etc.
     */
    public static function getRunTimes()
    {
        return 0;
    }
}