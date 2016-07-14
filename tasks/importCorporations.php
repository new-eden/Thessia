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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thessia\Lib\Db;

class importCorporations extends Command
{
    protected function configure()
    {
        $this
            ->setName("importCorporations")
            ->setDescription("Import corporations from zKB to Thessia...");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get the container
        $container = getContainer();

        /** @var Db $db */
        $db = $container->get("db");
        /** @var \MongoClient $mongo */
        $mongo = $container->get("mongo");
        /** @var Collection $collection */
        $collection = $mongo->selectCollection("thessia", "corporations");

        $run = true;
        $offset = 0;
        $limit = 100000;
        do {
            $corporations = $db->query("SELECT corporationID, allianceID, name, ceoID, ticker, memberCount, lastUpdated, information FROM zkillboard.zz_corporations");
            if (empty($corporations)) {
                $run = false;
            }

            foreach ($corporations as $corporation) {
                //$exists = $collection->findOne(array("characterID" => $character["characterID"]));
                //if(!empty($exists) || !is_null($exists)) {
                //    echo "Character already exists in database, skipping...\n";
                //    continue;
                //}

                echo "Importing {$corporation["name"]}...\n";
                $data = array(
                    "corporationID" => $corporation["corporationID"],
                    "corporationName" => $corporation["name"],
                    "allianceID" => $corporation["allianceID"],
                    "allianceName" => $db->queryField("SELECT name FROM zkillboard.zz_alliances WHERE allianceID = :allianceID",
                        "name", array(":allianceID" => $corporation["allianceID"])),
                    "ceoID" => $corporation["ceoID"],
                    "ticker" => $corporation["ticker"],
                    "memberCount" => $corporation["memberCount"],
                    "lastUpdated" => $corporation["lastUpdated"],
                    "information" => array(),
                );

                if (isset($corporation["information"])) {
                    $data["information"] = json_decode($corporation["information"], true);
                }

                // Now insert it into the killmail collection
                try {
                    $count = $collection->insertOne($data)->getInsertedCount();
                    if ($count >= 1) {
                        echo "Inserting {$corporation["corporationID"]}... Offset: {$offset}...\n";
                    }

                } catch (\Exception $e) {

                }
            }
            $run = false;

            //$run = false;
        } while ($run == true);
    }
}