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
use Thessia\Helper\CrestHelper;
use Thessia\Helper\EVEApi\EVE;

class UpdateAllianceList {
    private $container;
    public function perform() {
        /** @var \Mongo $mongo */
        $$mongo = $this->container->get("mongo");
        /** @var Logger $log */
        $log = $this->container->get("log");
        /** @var EVE $eve */
        $eve = $this->container->get("ccpEVE");
        /** @var CrestHelper $crestHelper */
        $crestHelper = $this->container->get("crestHelper");
        /** @var Collection $collection */
        $collection = $mongo->selectCollection("thessia", "alliances");
        /** @var Collection $corporationCollection */
        $corporationCollection = $mongo->selectCollection("thessia", "corporations");

        $log->info("CRON: Inserting/Updating alliances...");
        $data = $eve->eveAllianceList();
        if(isset($data["result"]["alliances"]) && !empty($data["result"]["alliances"])) {
            foreach ($data["result"]["alliances"] as $alliance) {
                $allianceID = (int)$alliance["allianceID"];
                $moreData = $crestHelper->getAlliance($allianceID);
                $allianceName = $moreData["name"];
                $ticker = $moreData["shortName"];
                $memberCount = (int)$alliance["memberCount"];
                $executorCorpID = (int)$alliance["executorCorpID"];
                $executorCorpName = $moreData["executorCorporation"]["name"];
                $startDate = $alliance["startDate"];
                $description = $moreData["description"];

                $array = array(
                    "allianceID" => (int)$allianceID,
                    "allianceName" => $allianceName,
                    "ticker" => $ticker,
                    "memberCount" => (int)$memberCount,
                    "executorCorporationID" => (int)$executorCorpID,
                    "executorCorporationName" => $executorCorpName,
                    "startDate" => $startDate,
                    "description" => $description,
                    "lastUpdated" => date("Y-m-d H:i:s")
                );

                $corpIDs = array();
                foreach ($moreData["corporations"] as $corp) {
                    $corpID = $corp["id"];
                    $corpIDs[] = $corpID;
                    \Resque::enqueue("low", '\Thessia\Tasks\Resque\UpdateCorporation',
                        array("corporationID" => $corpID));
                }

                $array["corporations"] = $corporationCollection->find(array("corporationID" => array("\$in" => $corpIDs)),
                    array("projection" => array("_id" => 0)))->toArray();
                try {
                    $collection->insertOne($array);
                } catch (\Exception $e) {
                    $collection->replaceOne(array("allianceID" => $allianceID), $array);
                }
            }
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