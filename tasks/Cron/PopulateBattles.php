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

class PopulateBattles {
    private $container;
    public function perform() {
        $log = $this->container->get("log");
        $mongo = $this->container->get("mongo");
        $killmails = $mongo->selectCollection("thessia", "killmails");
        $storage = $mongo->selectCollection("thessia", "storage");
        $battleCollection = $mongo->selectCollection("thessia", "battles");

        /*
         * look over the killmail table in 2 hour chunks, each time advancing one hour, till there is a hit.
         * Then go over a 4 hour chunk of time, in 10 minute intervals to check for hits of 3 kills or more.
         * example: startTime = 2009-01-01 22:00:00, endTime = 2009-01-01 00:00:00
         * A hit has been made in that two hour chunk, of 500 people.
         * Expand it by a 30 minutes in each direction (3 hours total) so start is 21:30 and end is 00:30 the next day.
         * Then look for killmails in a 10 minute interval
         * First hit of 3 kills or more, is registered as the start.
         * Once 6 chunks have registered false (meaning less than 3 kills), call the battle finished, and move forward an hour
         * It can go beyond 00:30 if it keeps getting hits of 3 kills or more.
         */
        //$startTime = strtotime(date("2007-12-05 20:00:00"));
        $endTime = time();
        $searchTime = time() - 7200;

        do {
            // Look for stuff from two hours ago, till now..
            $timeAfter = new UTCDateTime((time() - 7200) * 1000);
            $timeBefore = new UTCDateTime((time()) * 1000);

            // Main aggregation pipeline
            $aggregation = array(
                array('$match' => array("killTime" => array('$gte' => $timeAfter, '$lte' => $timeBefore))),
                array('$group' => array('_id' => '$solarSystemID', 'count' => array('$sum' => 1))),
                array('$project' => array("_id" => 0, "count" => '$count', "solarSystemID" => '$_id')),
                array('$match' => array('count' => array('$gte' => 50))),
                array('$sort' => array('count' => -1))
            );

            // Run the pipeline
            $result = $killmails->aggregate($aggregation)->toArray();
            // If there are results, we'll start to drill down into it
            if(count($result) >= 1) {
                $log->addInfo("Found " . count($result) . " battle(s)");
                foreach($result as $battle) {
                    $log->addInfo(count($result) . " battle(s) was found with " . $battle["count"] . " participants. Start to drill down.");
                    $run = true;
                    $fails = 0;
                    $minTime = $searchTime - 3600;
                    $solarSystemID = $battle["solarSystemID"];
                    $battleStartTime = 0;

                    // Check if there has already been found a battle in this system, taking place within the last hour (and max 4 hours forward)
                    $searchArray = array("solarSystemInfo.solarSystemID" => $solarSystemID, "startTime" => array('$gte' => new UTCDateTime(($minTime - 14400) * 1000))); //, "endTime" => array('$lte' => new UTCDateTime(($minTime + 14400) * 1000)));
                    $battleCount = $battleCollection->find($searchArray)->toArray();
                    if(!empty($battleCount)) {
                        continue;
                    }

                    do {
                        $timeAfter = new UTCDateTime($minTime * 1000);
                        $timeBefore = new UTCDateTime(($minTime + 600) * 1000);

                        // Main aggregation pipeline
                        $aggregation = array(
                            array('$match' => array("solarSystemID" => $solarSystemID, "killTime" => array('$gte' => $timeAfter, '$lte' => $timeBefore))),
                            array('$group' => array('_id' => '$solarSystemID', 'count' => array('$sum' => 1))),
                            array('$project' => array("_id" => 0, "count" => '$count', "solarSystemID" => '$_id')),
                            array('$match' => array('count' => array('$gte' => 3))),
                            array('$sort' => array('count' => -1))
                        );

                        // Run the pipeline
                        $result = $killmails->aggregate($aggregation)->toArray();

                        // Set the startTime of the battle
                        if (count($result) >= 1 && $battleStartTime == 0) {
                            $fails = 0;
                            $battleStartTime = $minTime;
                        }

                        // It's all over.. (Time to log everything we've learned
                        if ($fails >= 20 && $battleStartTime != 0) {
                            $log->addInfo("A battle happened in {$solarSystemID}, and started at " . date("Y-m-d H:i:s", $battleStartTime) . " and ended at " . date("Y-m-d H:i:s", $minTime - 12000));

                            // Process the battle report
                            $this->processBattleReport($battleStartTime, $minTime - 12000, $solarSystemID);

                            // This one is done, lets call it quits and find more!
                            $run = false;
                        } elseif ($fails >= 20) {
                            $run = false;
                        }

                        // Increment minTime by 10 minutes (In the end, it will increment minTime by 3600, meaning we have to subtract 3600 from minTime to get endTime
                        $minTime = $minTime + 600;

                        // If the array is empty, it's a fail, and we'll try again
                        if (count($result) == 0) {
                            $fails++;
                            continue;
                        }
                    } while ($run == true);
                }
            }

            // Increment searchTime by an hour
            $searchTime = $searchTime + 3600;

            // Save how far we've come in the db
            $storage->replaceOne(array("key" => "battleImporterStartTime"), array("key" => "battleImporterStartTime", "value" => $searchTime), array("upsert" => true));
        } while($searchTime < $endTime);
    }

    private function processBattleReport($startTime, $endTime, $solarSystemID) {
        $mongo = $this->container->get("mongo");
        $collection = $mongo->selectCollection("thessia", "battles");
        $killmails = $mongo->selectCollection("thessia", "killmails");
        $solarSystemCollection = $mongo->selectCollection("ccp", "solarSystems");

        // Find the kills in the system that happened for this battle
        $findArray = array(
            array('$match' => array("solarSystemID" => $solarSystemID, "killTime" => array('$gte' => new UTCDateTime($startTime * 1000), '$lte' => new UTCDateTime($endTime * 1000)))),
            array('$unwind' => '$attackers')
        );
        $killData = $killmails->aggregate($findArray)->toArray();

        foreach($killData as $key => $val) {
            unset($killData[$key]["osmium"]);
        }

        $redTeam = array();
        $redTeamCharacters = array();
        $redTeamShips = array();
        $redTeamCorporations = array();
        $redTeamKills = array();
        $blueTeam = array();
        $blueTeamCharacters = array();
        $blueTeamShips = array();
        $blueTeamCorporations = array();
        $blueTeamKills = array();
        $success = $this->findTeams($redTeam, $blueTeam, $killData);
        if($success == false)
            return;

        foreach($redTeam as $member) {
            foreach($killData as $mail) {
                if($mail["attackers"]["allianceName"] == $member) {
                    if($mail["attackers"]["corporationName"] != "" && !in_array($mail["attackers"]["corporationName"], $redTeamCorporations))
                        $redTeamCorporations[] = $mail["attackers"]["corporationName"];

                    if(!in_array($mail["attackers"]["characterName"], $redTeamCharacters)) {
                        if (!isset($redTeamShips[$mail["attackers"]["shipTypeName"]])) {
                            $redTeamShips[$mail["attackers"]["shipTypeName"]] = array(
                                "shipTypeName" => $mail["attackers"]["shipTypeName"],
                                "count" => 1
                            );
                        } else {
                            $redTeamShips[$mail["attackers"]["shipTypeName"]]["count"]++;
                        }
                    }

                    if(!in_array($mail["attackers"]["characterName"], $redTeamCharacters))
                        $redTeamCharacters[] = $mail["attackers"]["characterName"];

                    if(!in_array($mail["killID"], $redTeamKills))
                        $redTeamKills[] = $mail["killID"];
                }
            }
        }
        foreach($blueTeam as $member) {
            foreach($killData as $mail) {
                if($mail["attackers"]["allianceName"] == $member) {
                    if($mail["attackers"]["corporationName"] != "" && !in_array($mail["attackers"]["corporationName"], $blueTeamCorporations))
                        $blueTeamCorporations[] = $mail["attackers"]["corporationName"];

                    if(!in_array($mail["attackers"]["characterName"], $blueTeamCharacters)) {
                        if (!isset($blueTeamShips[$mail["attackers"]["shipTypeName"]])) {
                            $blueTeamShips[$mail["attackers"]["shipTypeName"]] = array(
                                "shipTypeName" => $mail["attackers"]["shipTypeName"],
                                "count" => 1
                            );
                        } else {
                            $blueTeamShips[$mail["attackers"]["shipTypeName"]]["count"]++;
                        }
                    }

                    if(!in_array($mail["attackers"]["characterName"], $blueTeamCharacters))
                        $blueTeamCharacters[] = $mail["attackers"]["characterName"];

                    if(!in_array($mail["killID"], $blueTeamKills))
                        $blueTeamKills[] = $mail["killID"];
                }
            }
        }

        // Remove the overlap
        if(count($blueTeamKills) > count($redTeamKills)) {
            foreach($blueTeamKills as $key => $id) {
                if(in_array($id, $redTeamKills))
                    unset($blueTeamKills[$key]);
            }
        } else {
            foreach($redTeamKills as $key => $id) {
                if(in_array($id, $blueTeamKills))
                    unset($redTeamKills[$key]);
            }
        }

        if(!empty($redTeam) && !empty($blueTeam) && !empty($redTeamCorporations) && !empty($blueTeamCorporations)) {
            // Quite possibly hilariously incorrect...
            $dataArray = array(
                "startTime" => new UTCDateTime($startTime * 1000),
                "endTime" => new UTCDateTime($endTime * 1000),
                "solarSystemInfo" => $solarSystemCollection->findOne(array("solarSystemID" => $solarSystemID)),
                "teamRed" => array(
                    "characters" => array_values($redTeamCharacters),
                    "corporations" => array_values($redTeamCorporations),
                    "alliances" => array_values($redTeam),
                    "ships" => array_values($redTeamShips),
                    "kills" => array_values($redTeamKills)
                ),
                "teamBlue" => array(
                    "characters" => array_values($blueTeamCharacters),
                    "corporations" => array_values($blueTeamCorporations),
                    "alliances" => array_values($blueTeam),
                    "ships" => array_values($blueTeamShips),
                    "kills" => array_values($blueTeamKills)
                )
            );
            $battleID = md5(json_encode($dataArray));
            $dataArray["battleID"] = $battleID;

            // Insert the data to the battles table
            try {
                $collection->replaceOne(array("battleID" => $battleID), $dataArray, array("upsert" => true));
            } catch(\Exception $e) {
                var_dump("Welp, an error happened.. " . $e->getMessage());
            }
        }
    }

    /*
     *
     */
    private function findTeams(&$redTeam, &$blueTeam, $killData) {
        $allianceTempArray = array();
        $corporationTempArray = array();

        // Should i map on alliances?
        $this->allianceSides($allianceTempArray, $blueTeam, $redTeam, $killData);
        if(empty($blueTeam) || empty($redTeam))
            $this->corporationSides($corporationTempArray, $blueTeam, $redTeam, $killData);
        if(empty($blueTeam) || empty($redTeam)) {
            return false;
        }

        // teamSizes
        $redTeamSize = count($redTeam);
        $blueTeamSize = count($blueTeam);

        if($redTeamSize > $blueTeamSize) {
            foreach($redTeam as $key => $player) {
                if(in_array($player, $blueTeam))
                    unset($redTeam[$key]);

                if($player == "")
                    unset($redTeam[$key]);
            }
        } else {
            foreach($blueTeam as $key => $player) {
                if(in_array($player, $redTeam))
                    unset($blueTeam[$key]);

                if($player == "")
                    unset($blueTeam[$key]);
            }

        }

        return true;
    }

    private function allianceSides(&$allianceTempArray, &$blueTeam, &$redTeam, $killData) {
        foreach($killData as $data) {
            $attacker = $data["attackers"];
            $victim = $data["victim"];

            if (isset($attacker["allianceName"]) && isset($victim["allianceName"]) && $victim["allianceName"] != "") {
                if (@!in_array($attacker["allianceName"], $allianceTempArray[$victim["allianceName"]])) {
                    $allianceTempArray[$victim["allianceName"]][] = $attacker["allianceName"];
                } else {
                    continue;
                }
            }
        }

        // Clean up any strings that are empty
        foreach($allianceTempArray as $key => $innerArray) {
            foreach($innerArray as $ikey => $welp) {
                if($welp == "")
                    unset($allianceTempArray[$key][$ikey]);
            }
        }

        // Find the red team
        $size = 0;
        $redTeamVictim = "";
        foreach($allianceTempArray as $victim => $attacker) {
            $attackers = count($attacker);
            if($attackers > $size) {
                $size = $attackers;
                $redTeam = $attacker;
                $redTeamVictim = $victim;
            }
        }
        // Unset the redTeam from the tempArray
        unset($allianceTempArray[$redTeamVictim]);

        // Find the blue team
        foreach($allianceTempArray as $blue) {
            foreach($blue as $member) {
                if(!in_array($member, $blueTeam))
                    $blueTeam[] = $member;
            }
        }
    }

    private function corporationSides(&$corporationTempArray, &$blueTeam, &$redTeam, $killData) {
        foreach($killData as $data) {
            $attacker = $data["attackers"];
            $victim = $data["victim"];

            if (isset($attacker["corporationName"]) && isset($victim["corporationName"]) && $victim["corporationName"] != "") {
                if (@!in_array($attacker["corporationName"], $corporationTempArray[$victim["corporationName"]])) {
                    $corporationTempArray[$victim["corporationName"]][] = $attacker["corporationName"];
                } else {
                    continue;
                }
            }
        }

        // Clean up any strings that are empty
        foreach($corporationTempArray as $key => $innerArray) {
            foreach($innerArray as $ikey => $welp) {
                if($welp == "")
                    unset($corporationTempArray[$key][$ikey]);
            }
        }

        // Find the red team
        $size = 0;
        $redTeamVictim = "";
        foreach($corporationTempArray as $victim => $attacker) {
            $attackers = count($attacker);
            if($attackers > $size) {
                $size = $attackers;
                $redTeam = $attacker;
                $redTeamVictim = $victim;
            }
        }
        // Unset the redTeam from the tempArray
        unset($corporationTempArray[$redTeamVictim]);

        // Find the blue team
        foreach($corporationTempArray as $blue) {
            foreach($blue as $member) {
                if(!in_array($member, $blueTeam))
                    $blueTeam[] = $member;
            }
        }
    }

    /**
     * Defines how often the cronjob runs, every 1 second, every 60 seconds, every 86400 seconds, etc.
     */
    public function getRunTimes()
    {
        return 1800;
    }

    public function setUp()
    {
        $this->container = getContainer();
    }

    public function tearDown()
    {

    }
}