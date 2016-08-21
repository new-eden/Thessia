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

class importZKBMailsReverse extends Command
{
    protected function configure()
    {
        $this
            ->setName("importZKBMailsReverse")
            ->setDescription("Import killmails from zKB to Thessia...");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get the container
        $container = getContainer();

        /** @var Db $db */
        $db = $container->get("db");
        /** @var cURL $curl */
        $curl = $container->get("curl");
        /** @var Killmails $kms */
        $kms = $container->get("killmails");

        // Initial offset
        $increment = 86405; // Increment by a full day and 5 seconds to get to the next day...
        // Get the latest offset from the DB
        $date = (int)$db->queryField("SELECT value FROM storage WHERE `key` = :offset", "value", array(":offset" => "zkbDateOffSetReverse"), 0);
        if($date == 0)
            $date = time();

        do {
            $convertedDate = date("Ymd", $date);
            $output->writeln("Currently working on killmails from: {$convertedDate}...");
            // Once the date gets to the current date, and if it goes above it - just exit..
            if($date < 1196889200) {
                // Reset the date to zero, so we can start all over again
                $db->execute("REPLACE INTO storage (`key`, value) VALUES (:key, :value)", array(":key" => "zkbDateOffSetReverse", ":value" => 0));
                $date = time();
                continue;
                //exit;
            }

            $killmails = json_decode($curl->getData("https://zkillboard.com/api/history/{$convertedDate}/"), true);
            if(count($killmails) > 0) {
                $output->writeln("Got " . count($killmails) . " killmails to rummage through...");
                foreach ($killmails as $killID => $crestHash) {
                    $killID = (int)$killID;
                    $crestHash = (string)$crestHash;

                    // Make sure the killmail doesn't already exist
                    $exists = $kms->getAllByKillID($killID)->toArray();
                    if (!empty($exists)) {
                        continue;
                    }

                    // Throw the killmail at resque for processing
                    \Resque::enqueue("high", '\Thessia\Tasks\Resque\KillmailParser', array("killID" => $killID, "killHash" => $crestHash));
                }
            }

            // New offset
            $db->execute("REPLACE INTO storage (`key`, value) VALUES (:key, :value)", array(":key" => "zkbDateOffSetReverse", ":value" => $date));

            // Increment the date
            $date = $date - $increment;
        } while (true); //!empty($killmails));
    }
}
