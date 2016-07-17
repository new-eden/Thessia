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

namespace Thessia\Tasks;

use MongoDB\Collection;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thessia\Model\Database\regions;
use Thessia\Model\Database\solarSystems;
use Thessia\Model\Database\typeIDs;

class populateAllianceList extends Command
{
    protected function configure()
    {
        $this
            ->setName("populateAllianceList")
            ->setDescription("Populates/Updates the alliance list...");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get the container
        $container = getContainer();

        /** @var \Mongo $mongo */
        $mongo = $container->get("mongo");
        /** @var Logger $log */
        $log = $container->get("log");
        /** @var Collection $collection */
        $collection = $mongo->selectCollection("thessia", "alliances");
        /** @var Collection $corporationCollection */
        $corporationCollection = $mongo->selectCollection("thessia", "corporations");

        $log->info("Inserting/Updating alliances...");
        $data = json_decode(json_encode(simplexml_load_string(file_get_contents("https://api.eveonline.com/eve/AllianceList.xml.aspx"))),
            true);
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
                "allianceID" => $allianceID,
                "allianceName" => $allianceName,
                "ticker" => $ticker,
                "memberCount" => $memberCount,
                "executorCorporationID" => $executorCorpID,
                "executorCorporationName" => $executorCorpName,
                "startDate" => $startDate,
                "description" => $description,
                "lastUpdated" => date("Y-m-d H:i:s")
            );

            // @todo make it insert it's corporations into the corporations table.. need to add a resque scheduler for updating corporations and whatnots tho..
            $corpIDs = array();
            foreach ($moreData["corporations"] as $corp) {
                $corpIDs[] = $corp["id"];
            }

            $array["corporations"] = $corporationCollection->find(array("corporationID" => array("\$in" => $corpIDs)),
                array("projection" => array("_id" => 0)))->toArray();

            $log->info("Adding/Updating {$allianceName}");
            try {
                $collection->insertOne($array);
            } catch (\Exception $e) {
                $collection->replaceOne(array("allianceID" => $allianceID), $array);
            }
        }
    }
}