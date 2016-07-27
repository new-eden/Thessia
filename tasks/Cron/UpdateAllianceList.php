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

class UpdateAllianceList
{
    /**
     * @param Container $container
     */
    public static function execute(Container $container)
    {
        /** @var \Mongo $mongo */
        $mongo = $container->get("mongo");
        /** @var Logger $log */
        $log = $container->get("log");
        /** @var Collection $collection */
        $collection = $mongo->selectCollection("thessia", "alliances");
        /** @var Collection $corporationCollection */
        $corporationCollection = $mongo->selectCollection("thessia", "corporations");

        $log->info("CRON: Inserting/Updating alliances...");
        $data = json_decode(json_encode(simplexml_load_string(file_get_contents("https://api.eveonline.com/eve/AllianceList.xml.aspx"))), true);
        foreach ($data["result"]["rowset"]["row"] as $alliance) {
            $alliData = $alliance["@attributes"];
            $allianceID = $alliData["allianceID"];
            $moreData = json_decode(file_get_contents("https://crest-tq.eveonline.com/alliances/{$allianceID}/"), true);
            $allianceName = $moreData["name"];
            $ticker = $moreData["shortName"];
            $memberCount = $alliData["memberCount"];
            $executorCorpID = $alliData["executorCorpID"];
            $executorCorpName = $moreData["executorCorporation"]["name"];
            $startDate = $alliData["startDate"];
            $description = $moreData["description"];

            $array = array(
                "allianceID" => (int) $allianceID,
                "allianceName" => $allianceName,
                "ticker" => $ticker,
                "memberCount" => (int) $memberCount,
                "executorCorporationID" => (int) $executorCorpID,
                "executorCorporationName" => $executorCorpName,
                "startDate" => $startDate,
                "description" => $description,
                "lastUpdated" => date("Y-m-d H:i:s")
            );

            $corpIDs = array();
            foreach ($moreData["corporations"] as $corp) {
                $corpID = $corp["id"];
                $corpIDs[] = $corpID;
                \Resque::enqueue("low", '\Thessia\Tasks\Resque\UpdateCorporation', array("corporationID" => $corpID));
            }

            $array["corporations"] = $corporationCollection->find(array("corporationID" => array("\$in" => $corpIDs)), array("projection" => array("_id" => 0)))->toArray();
            $log->info("CRON UpdateAllianceList: Updating {$allianceName}");
            try {
                $collection->insertOne($array);
            } catch (\Exception $e) {
                $collection->replaceOne(array("allianceID" => $allianceID), $array);
            }
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