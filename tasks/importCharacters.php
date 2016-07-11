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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Thessia\Lib\Db;
use Thessia\Model\Database\regions;
use Thessia\Model\Database\solarSystems;
use Thessia\Model\Database\typeIDs;
use Thessia\Model\EVE\Crest;

class importCharacters extends Command
{
    protected function configure()
    {
        $this
            ->setName("importCharacters")
            ->setDescription("Import characters from zKB to Thessia...");
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
        $collection = $mongo->selectCollection("thessia", "characters");

        $run = true;
        $offset = 600000;
        $limit = 100000;
        do {
            $characters = $db->query("SELECT characterID, corporationID, allianceID, name, lastUpdated, history FROM zkillboard.zz_characters WHERE characterID > 0 AND name != '' LIMIT :offset,:limit", array(":offset" => $offset, ":limit" => $limit));
            if(empty($characters))
                $run = false;

            foreach($characters as $character) {
                //$exists = $collection->findOne(array("characterID" => $character["characterID"]));
                //if(!empty($exists) || !is_null($exists)) {
                //    echo "Character already exists in database, skipping...\n";
                //    continue;
                //}

                echo "Importing {$character["name"]}...\n";
                $data = array(
                    "characterID" => $character["characterID"],
                    "characterName" => $character["name"],
                    "corporationID" => $character["corporationID"],
                    "corporationName" => $db->queryField("SELECT name FROM zkillboard.zz_corporations WHERE corporationID = :corporationID", "name", array(":corporationID" => $character["corporationID"])),
                    "allianceID" => $character["allianceID"],
                    "allianceName" => $db->queryField("SELECT name FROM zkillboard.zz_alliances WHERE allianceID = :allianceID", "name", array(":allianceID" => $character["allianceID"])),
                    "lastUpdated" => $character["lastUpdated"],
                    "history" => array(),
                );

                if(isset($character["history"]))
                    $data["history"] = json_decode($character["history"], true)["employmentHistory"];

                // Now insert it into the killmail collection
                try {
                    echo "Inserting {$character["characterID"]}... Offset: {$offset}...\n";
                    $collection->insertOne($data);
                } catch (\Exception $e) {

                }
            }
            $offset = $offset + $limit;

            //$run = false;
        } while($run == true);
    }
}