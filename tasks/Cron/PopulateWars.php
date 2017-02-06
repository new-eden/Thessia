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
 * Date: 27-07-2016
 * Time: 22:48
 */

namespace Thessia\Tasks\Cron;


use League\Container\Container;
use MongoDB\BSON\UTCDatetime;
use MongoDB\Collection;
use Monolog\Logger;
use Thessia\Helper\CrestHelper;

class PopulateWars {
    private $container;
    public function perform()
    {
        /** @var \MongoClient $mongo */
        $mongo = $this->container->get("mongo");
        /** @var Logger $log */
        $log = $this->container->get("log");
        /** @var CrestHelper $crestHelper */
        $crestHelper = $this->container->get("crestHelper");
        /** @var Collection $collection */
        $collection = $mongo->selectCollection("thessia", "wars");

        $log->addInfo("CRON: Updating Wars from CREST");
        $data = $crestHelper->getWars();
        $pageCount = $data["pageCount"] ?? 1;
        $currPage = 1;

        while($currPage <= $pageCount) {
            $data = $crestHelper->getWars($currPage);
            foreach($data["items"] as $war) {
                $innerData = $crestHelper->getWar($war["id"]);

                $warID = (int) $innerData["id"];

                if(isset($warID)) {
                    // If it already exists in the database, and timeFinished is set - just skip
                    $exists = $collection->findOne(array("warID" => $warID));
                    if (!empty($exists) && isset($innerData["timeFinished"]))
                        continue;

                    // Create the war data array
                    $array = array(
                        "warID" => $warID,
                        "timeDeclared" => isset($innerData["timeDeclared"]) ? new UTCDatetime(strtotime($innerData["timeDeclared"]) * 1000) : null,
                        "timeStarted" => isset($innerData["timeStarted"]) ? new UTCDatetime(strtotime($innerData["timeStarted"]) * 1000) : null,
                        "timeFinished" => isset($innerData["timeFinished"]) ? new UTCDatetime(strtotime($innerData["timeFinished"]) * 1000) : null,
                        "openForAllies" => (boolean)$innerData["openForAllies"],
                        "allyCount" => (int)$innerData["allyCount"],
                        "mutual" => (boolean)$innerData["mutual"]
                    );

                    // Populate the aggressor part of the war data array
                    $array["aggressor"] = array(
                        "shipsKilled" => $innerData["aggressor"]["shipsKilled"],
                        "iskKilled" => $innerData["aggressor"]["iskKilled"],
                        "aggressorName" => $innerData["aggressor"]["name"],
                        "aggressorID" => $innerData["aggressor"]["id"]
                    );

                    // Populate the defender part of the war data array
                    $array["defender"] = array(
                        "shipsKilled" => $innerData["defender"]["shipsKilled"],
                        "iskKilled" => $innerData["defender"]["iskKilled"],
                        "defenderName" => $innerData["defender"]["name"],
                        "defenderID" => $innerData["defender"]["id"]
                    );

                    // Insert the war data array into the database
                    $collection->replaceOne(array("warID" => $innerData["id"]), $array, array("upsert" => true));

                    // Now pass off the killmail link to the Resque fetcher, so it can fetch all the killmails.. If there are any.
                    if ($innerData["aggressor"]["shipsKilled"] > 0 || $innerData["defender"]["shipsKilled"] > 0) {
                        $totalCount = count($innerData["aggressor"]["shipsKilled"]) + count($innerData["defender"]["shipsKilled"]);
                        $log->addInfo("CRON (Wars): Sending {$totalCount} kills to the parser...");
                        \Resque::enqueue("med", '\Thessia\Tasks\Resque\PopulateWarKillmails', array("href" => $innerData["killmails"], "warID" => $innerData["id"]));
                    }
                }
            }

            // Increment the current page
            $currPage++;
        }

        exit;
    }

    /**
     * Defines how often the cronjob runs, every 1 second, every 60 seconds, every 86400 seconds, etc.
     */
    public function getRunTimes()
    {
        return 86400;
    }

    public function setUp()
    {
        $this->container = getContainer();
    }

    public function tearDown()
    {

    }
}