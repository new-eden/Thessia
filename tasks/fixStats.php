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

class fixStats extends Command
{
    protected function configure()
    {
        $this
            ->setName("fixStats")
            ->setDescription("Fixes the kills/losses/points stats on the char/corp/alli pages");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get the container
        $container = getContainer();

        /** @var \MongoClient $mongo */
        $mongo = $container->get("mongo");
        /** @var Collection $collection */
        $killmails = $mongo->selectCollection("thessia", "killmails");

        // Get all characterIDs
        $characters = $mongo->selectCollection("thessia", "characters");
        $chars = $characters->find();

        $fixd = 0;
        foreach($chars as $char) {
            $k = $killmails->aggregate(array(
                array('$match' => array("attackers.characterID" => $char["characterID"])),
                array('$group' => array("_id" => null, "kills" => array('$sum' => 1))),
                array('$project' => array("_id" => 0, "kills" => '$kills'))
            ))->toArray();
            $l = $killmails->aggregate(array(
                array('$match' => array("victim.characterID" => $char["characterID"])),
                array('$group' => array("_id" => null, "losses" => array('$sum' => 1))),
                array('$project' => array("_id" => 0, "losses" => '$losses'))
            ))->toArray();
            $p = $killmails->aggregate(array(
                array('$match' => array("attackers.characterID" => $char["characterID"])),
                array('$group' => array("_id" => null, "totalPoints" => array('$sum' => '$pointValue'))),
                array('$project' => array("_id" => 0, "points" => '$totalPoints'))
            ))->toArray();

            $kills = 0;
            $losses = 0;
            $points = 0;
            if(!empty($k))
                $kills = $k[0]["kills"];
            if(!empty($l))
                $losses = $l[0]["losses"];
            if(!empty($p))
                $points = $p[0]["points"];

            $char["kills"] = $kills;
            $char["losses"] = $losses;
            $char["points"] = $points;

            $characters->replaceOne(array("characterID" => $char["characterID"]), $char);
            if($fixd % 500 == 0)
                $output->writeln("Fixed {$fixd} characters..");
            $fixd++;
        }
        $output->writeln("Fixed {$fixd} characters..");

        // Get all corporationIDs
        $corporations = $mongo->selectCollection("thessia", "corporations");
        $corps = $corporations->find();

        $fixd = 0;
        foreach($corps as $corp) {
            $k = $killmails->aggregate(array(
                array('$match' => array("attackers.corporationID" => $corp["corporationID"])),
                array('$group' => array("_id" => null, "kills" => array('$sum' => 1))),
                array('$project' => array("_id" => 0, "kills" => '$kills'))
            ))->toArray();
            $l = $killmails->aggregate(array(
                array('$match' => array("victim.corporationID" => $corp["corporationID"])),
                array('$group' => array("_id" => null, "losses" => array('$sum' => 1))),
                array('$project' => array("_id" => 0, "losses" => '$losses'))
            ))->toArray();
            $p = $killmails->aggregate(array(
                array('$match' => array("attackers.corporationID" => $corp["corporationID"])),
                array('$group' => array("_id" => null, "totalPoints" => array('$sum' => '$pointValue'))),
                array('$project' => array("_id" => 0, "points" => '$totalPoints'))
            ))->toArray();

            $kills = 0;
            $losses = 0;
            $points = 0;
            if(!empty($k))
                $kills = $k[0]["kills"];
            if(!empty($l))
                $losses = $l[0]["losses"];
            if(!empty($p))
                $points = $p[0]["points"];

            $corp["kills"] = $kills;
            $corp["losses"] = $losses;
            $corp["points"] = $points;

            $corporations->replaceOne(array("corporationID" => $corp["corporationID"]), $corp);
            if($fixd % 500 == 0)
                $output->writeln("Fixed {$fixd} corporations..");
            $fixd++;
        }
        $output->writeln("Fixed {$fixd} corporations..");

        // Get all allianceIDs
        $alliances = $mongo->selectCollection("thessia", "alliances");
        $allis = $alliances->find();

        $fixd = 0;
        foreach($allis as $alli) {
            $k = $killmails->aggregate(array(
                array('$match' => array("attackers.allianceID" => $alli["allianceID"])),
                array('$group' => array("_id" => null, "kills" => array('$sum' => 1))),
                array('$project' => array("_id" => 0, "kills" => '$kills'))
            ))->toArray();
            $l = $killmails->aggregate(array(
                array('$match' => array("victim.allianceID" => $alli["allianceID"])),
                array('$group' => array("_id" => null, "losses" => array('$sum' => 1))),
                array('$project' => array("_id" => 0, "losses" => '$losses'))
            ))->toArray();
            $p = $killmails->aggregate(array(
                array('$match' => array("attackers.allianceID" => $alli["allianceID"])),
                array('$group' => array("_id" => null, "totalPoints" => array('$sum' => '$pointValue'))),
                array('$project' => array("_id" => 0, "points" => '$totalPoints'))
            ))->toArray();

            $kills = 0;
            $losses = 0;
            $points = 0;
            if(!empty($k))
                $kills = $k[0]["kills"];
            if(!empty($l))
                $losses = $l[0]["losses"];
            if(!empty($p))
                $points = $p[0]["points"];

            $alli["kills"] = $kills;
            $alli["losses"] = $losses;
            $alli["points"] = $points;

            $alliances->replaceOne(array("allianceID" => $alli["allianceID"]), $alli);
            if($fixd % 500 == 0)
                $output->writeln("Fixed {$fixd} alliances..");
            $fixd++;
        }
        $output->writeln("Fixed {$fixd} alliances..");
    }
}