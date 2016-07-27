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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thessia\Lib\Db;
use Thessia\Lib\cURL;
use Thessia\Model\Database\EVE\Killmails;
use Thessia\Model\EVE\Crest;
use Thessia\Model\EVE\Parser;

class importOldKillmails extends Command
{
    protected function configure()
    {
        $this
            ->setName("importOldKillmails")
            ->setDescription("Import killmails from zKB to Thessia...");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get the container
        $container = getContainer();

        /** @var Db $db */
        $db = $container->get("db");
        /** @var Parser $parser */
        $parser = $container->get("parser");
        /** @var Crest $collection */
        $crest = $container->get("crest");
        /** @var cURL $curl */
        $curl = $container->get("curl");
        /** @var Killmails $killmails */
        $kms = $container->get("killmails");

        // Get the latest offset from the DB
        $offset = (int)$db->queryField("SELECT value FROM storage WHERE `key` = :offset", "value", array(":offset" => "mailImportOffset"), 0);
        $limit = 100000;
        $run = true;

        echo "Starting offset: {$offset}...\n";

        do {
            $totalCnt = 0;
            $innerCnt = 0;
            $killmails = $db->query("SELECT killID, kill_json, hash FROM zkillboard.zz_killmails WHERE killID > :killID ORDER BY killID ASC LIMIT :limit", array(":killID" => $offset, ":limit" => $limit));

            echo "Current offset: {$offset}...\n";
            foreach ($killmails as $killmail) {
                $exists = $kms->getAllByKillID($killmail["killID"]);
                if(!empty($exists)) {
                    // Set the offset
                    $offset = $killmail["killID"];
                    continue;
                }

                if ($innerCnt == 1000) {
                    $innerCnt = 0;
                    $tmpOffset = $killmail["killID"];
                    echo "Updating offset to {$tmpOffset}...\n";
                    $db->execute("REPLACE INTO storage (`key`, value) VALUES (:key, :value)", array(":key" => "mailImportOffset", ":value" => $tmpOffset));
                }

                $killID = $killmail["killID"];

                $json = json_decode($killmail["kill_json"], true);

                // Generate crest hash
                $killHash = $crest->generateHash($json);

                // Throw the killmail at resque for processing
                \Resque::enqueue("high", '\Thessia\Tasks\Resque\KillmailParser', array("killID" => $killID, "killHash" => $killHash));

                // Increment counters
                $innerCnt++;
                $totalCnt++;

                // Sleep for a bit, so we don't overwhelm resque..
                usleep(30000);

                // Set the offset
                $offset = $killmail["killID"];
            }

            // New offset
            echo "Storing new offset in database...\n";
            $db->execute("REPLACE INTO storage (`key`, value) VALUES (:key, :value)", array(":key" => "mailImportOffset", ":value" => $offset));
            echo date("Y-m-d H:i:s") . ": Done with the first {$limit} from {$offset}, now going on to the next {$limit} from {$offset}...\n";
        } while ($run == true);
    }
}
