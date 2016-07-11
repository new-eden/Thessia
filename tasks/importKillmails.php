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

class importKillmails extends Command
{
    protected function configure()
    {
        $this
            ->setName("importKillmails")
            ->addOption("order", "o", InputOption::VALUE_REQUIRED, "The order to fetch data in", "asc")
            ->setDescription("Import killmails from zKB to Thessia...");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get the container
        $container = getContainer();

        /** @var Db $db */
        $db = $container->get("db");
        /** @var \MongoClient $mongo */
        $mongo = $container->get("mongo");
        /** @var Crest $collection */
        $crest = $container->get("crest");
        /** @var typeIDs $typeIDs */
        $typeIDs = $container->get("typeIDs");
        /** @var solarSystems $solarSystems */
        $solarSystems = $container->get("solarSystems");
        /** @var regions $regions */
        $regions = $container->get("regions");
        /** @var Collection $collection */
        $collection = $mongo->selectCollection("thessia", "killmails");

        // Get the order to fetch data in
        $validOrder = array("asc", "desc");
        $order = $input->getOption("order");
        if(!in_array(strtolower($order), $validOrder)) {
            echo "Error, not a valid fetch order...\n";
            exit();
        }
        // Upper case the shit out of it
        $order = strtoupper($order);


        // Get the latest offset from the DB
        $offset = (int) $db->queryField("SELECT value FROM storage WHERE `key` = :offset", "value", array(":offset" => "importOffset{$order}"), 0);
        $limit = 1000;
        $run = true;

        do {
            $killmails = $db->query("SELECT killID, kill_json, hash FROM zkillboard.zz_killmails WHERE killID > 0 ORDER BY killID {$order} LIMIT :offset,:limit", array(":offset" => $offset, ":limit" => $limit));
            foreach($killmails as $killmail) {
                $killID = $killmail["killID"];

                // @todo Check if the kill has been inserted into Mongo
                $exists = $collection->findOne(array("killID" => $killID));
                if(!empty($exists) || !is_null($exists)) {
                    echo "Kill already exists in database, skipping...\n";
                    continue;
                }

                $json = json_decode($killmail["kill_json"], true);
                $hash = $killmail["hash"];

                // Generate crest hash
                $killHash = $crest->generateHash($json);

                // Get killmail from CREST
                $url = "https://crest.eveonline.com/killmails/{$killID}/{$killHash}/";
                echo "Trying to fetch {$url}...\n";
                $data = json_decode(file_get_contents($url), true);

                // Generate the mail from CREST data
                $kmData = $crest->generateFromCREST(array("killID" => $killID, "killmail" => $data));

                // Image server URL
                $imageServer = "https://image.eveonline.com/";

                echo "Generating new killmail array...\n";
                // New killData array that is being built..
                $nk = array();

                $nk["killID"] = (int) $kmData["killID"];
                $nk["killTime"] = $kmData["killTime"];

                // Lets make sure that the data in question actually works, if it doesn't we'll just continue and leave it be for later.. (Could be CREST fucking up, who knows..
                if($nk["killTime"] == "") {
                    echo "Skipping {$killID} because of a malformed / missing killTime...\n";
                    continue;
                }

                echo "Generating top portion...\n";
                $nk["solarSystemID"] = (int)$kmData["solarSystemID"];
                $solarData = $solarSystems->getAllBySolarSystemID($kmData["solarSystemID"])->toArray()[0];
                $nk["solarSystemName"] = (int) $solarData["solarSystemID"];
                $nk["regionID"] = (int) $solarData["regionID"];
                $nk["regionName"] = $solarData["regionName"];
                $nk["near"] = $this->getNear($kmData["victim"]["x"], $kmData["victim"]["y"], $kmData["victim"]["z"], $kmData["solarSystemID"]);
                $nk["x"] = (float)$kmData["victim"]["x"];
                $nk["y"] = (float)$kmData["victim"]["y"];
                $nk["z"] = (float)$kmData["victim"]["z"];
                $nk["moonID"] = (int)$kmData["moonID"];
                $killValues = $this->calculateKillValue($kmData);
                $nk["shipValue"] = (float)$killValues["shipValue"];
                $nk["fittingValue"] = (float)$killValues["itemValue"];
                $nk["totalValue"] = (float)$killValues["totalValue"];
                $nk["dna"] = $this->getDNA($kmData["items"], $kmData["victim"]["shipTypeID"]);
                $nk["crestHash"] = $killHash;

                // Victim Data
                echo "Generating victim portion...\n";
                $nk["victim"]["x"] = (float)$kmData["victim"]["x"];
                $nk["victim"]["y"] = (float)$kmData["victim"]["y"];
                $nk["victim"]["z"] = (float)$kmData["victim"]["z"];
                $nk["victim"]["shipTypeID"] = (int)$kmData["victim"]["shipTypeID"];
                $shipData = $typeIDs->getAllByTypeID($kmData["victim"]["shipTypeID"])->toArray()[0];
                $nk["victim"]["shipTypeName"] = $shipData["name"]["en"];
                $nk["victim"]["shipImageURL"] = $imageServer . "Type/" . $kmData["victim"]["shipTypeID"] . "_32.png";
                $nk["victim"]["damageTaken"] = (int)$kmData["victim"]["damageTaken"];
                $nk["victim"]["characterID"] = (int)$kmData["victim"]["characterID"];
                $nk["victim"]["characterName"] = $kmData["victim"]["characterName"];
                $nk["victim"]["characterImageURL"] = $imageServer . "Character/" . $kmData["victim"]["characterID"] . "_128.jpg";
                $nk["victim"]["corporationID"] = (int)$kmData["victim"]["corporationID"];
                $nk["victim"]["corporationName"] = $kmData["victim"]["corporationName"];
                $nk["victim"]["corporationImageURL"] = $imageServer . "Corporation/" . $kmData["victim"]["corporationID"] . "_128.png";
                $nk["victim"]["allianceID"] = (int)$kmData["victim"]["allianceID"];
                $nk["victim"]["allianceName"] = $kmData["victim"]["allianceName"];
                $nk["victim"]["allianceImageURL"] = $imageServer . "Alliance/" . $kmData["victim"]["allianceID"] . "_128.png";
                $nk["victim"]["factionID"] = (int)$kmData["victim"]["factionID"];
                $nk["victim"]["factionName"] = $kmData["victim"]["factionName"];
                $nk["victim"]["factionImageURL"] = $imageServer . "Alliance/" . $kmData["victim"]["factionID"] . "_128.png";

                // Attacker data upgrade
                echo "Generating attacker portion...\n";
                foreach ($kmData["attackers"] as $attacker) {
                    $inner = array();
                    $inner["characterID"] = (int)$attacker["characterID"];
                    $inner["characterName"] = $attacker["characterName"];
                    $inner["characterImageURL"] = $imageServer . "Character/" . $attacker["characterID"] . "_128.jpg";
                    $inner["corporationID"] = (int)$attacker["corporationID"];
                    $inner["corporationName"] = $attacker["corporationName"];
                    $inner["corporationImageURL"] = $imageServer . "Corporation/" . $attacker["corporationID"] . "_128.png";
                    $inner["allianceID"] = (int)$attacker["allianceID"];
                    $inner["allianceName"] = $attacker["allianceName"];
                    $inner["allianceImageURL"] = $imageServer . "Alliance/" . $attacker["allianceID"] . "_128.png";
                    $inner["factionID"] = (int)$attacker["factionID"];
                    $inner["factionName"] = $attacker["factionName"];
                    $inner["factionImageURL"] = $imageServer . "Alliance/" . $attacker["factionID"] . "_128.png";
                    $inner["securityStatus"] = (float)$attacker["securityStatus"];
                    $inner["damageDone"] = (int)$attacker["damageDone"];
                    $inner["finalBlow"] = (int)$attacker["finalBlow"];
                    $inner["weaponTypeID"] = (int)$attacker["weaponTypeID"];
                    $weaponData = $typeIDs->getAllByTypeID($attacker["weaponTypeID"])->toArray()[0];
                    $inner["weaponTypeName"] = $weaponData["name"]["en"];
                    $inner["weaponImageURL"] = $imageServer . "Type/" . $attacker["weaponTypeID"] . "_32.png";
                    $inner["shipTypeID"] = (int)$attacker["shipTypeID"];
                    $shipData = $typeIDs->getAllByTypeID($attacker["shipTypeID"])->toArray()[0];
                    $inner["shipTypeName"] = $shipData["name"]["en"];
                    $inner["shipImageURL"] = $imageServer . "Type/" . $attacker["shipTypeID"] . "_32.png";

                    $nk["attackers"][] = $inner;
                }

                // Item data upgrade
                echo "Generating item portion...\n";
                foreach ($kmData["items"] as $item) {
                    $inner = array();
                    $inner["typeID"] = (int)$item["typeID"];
                    $typeData = $typeIDs->getAllByTypeID($item["typeID"])->toArray()[0];
                    $inner["typeName"] = $typeData["name"]["en"];
                    $inner["typeImageURL"] = $imageServer . "Type/" . $item["typeID"] . "_32.png";
                    $inner["groupID"] = $typeData["groupID"];
                    $inner["categoryID"] = $db->queryField("SELECT categoryID FROM rena.invGroups WHERE groupID = :groupID", "categoryID", array(":groupID" => $inner["groupID"]));
                    $inner["flag"] = (int)$item["flag"];
                    $inner["qtyDropped"] = (int)$item["qtyDropped"];
                    $inner["qtyDestroyed"] = (int)$item["qtyDestroyed"];
                    $inner["singleton"] = (int)$item["singleton"];
                    $inner["value"] = (float)$this->getPriceForTypeID($item["typeID"]);

                    $nk["items"][] = $inner;
                }

                // Osmium fitting information
                // URL: https://o.smium.org/api/json/loadout/dna/attributes/loc:ship,a:hiSlots,a:medSlots,a:lowSlots,a:upgradeSlotsLeft,a:tank,a:ehpAndResonances,a:capacitors,a:damage?input=17703:12563;2:31490;2:2605;2:5973;1:3041;2:1999;2:31442;1:3244;1:2048;1::
                // Need to get max DPS with stock ammo, just gotta load some ammo into it - must be a way to determine what to load
                echo "Getting data from Osmium...\n";
                $osmiumData = array();
                if (!empty($nk["items"])) // @todo fix so that osmium can actually go down without this going apeshit.. try/catch or something..
                    $osmiumData = json_decode(file_get_contents("https://o.smium.org/api/json/loadout/dna/attributes/loc:ship,a:tank,a:ehpAndResonances,a:capacitors,a:damage?input=" . $nk["dna"]), true);

                $nk["osmium"] = $osmiumData;

                // Now insert it into the killmail collection
                echo "Inserting {$killID}...\n";
                $collection->insertOne($nk);
            }

            // New offset
            $offset = $offset + $limit;
            echo "Storing new offset in database...\n";
            $db->execute("REPLACE INTO storage (`key`, value) VALUES (:key, :value)", array(":key" => "importOffset{$order}", ":value" => $offset));
            //$db->execute("INSERT INTO storage (`key`, value) VALUES (:offset, :newOffset) ON DUPLICATE KEY UPDATE value = :newOffset", array(":offset" => "importOffset{$order}", ":newOffset" => $offset));
            echo "Done with the first {$limit}, now going on to the next {$limit}...\n";
        } while($run == true);
    }

    private function getNear($x, $y, $z, $solarSystemID)
    {
        // Get the container
        $container = getContainer();

        /** @var Db $db */
        $db = $container->get("db");

        $data = $db->queryRow("SELECT TRUNCATE(SQRT(POW(:x - x, 2) + POW(:y - y, 2) + POW(:z - z, 2)), 2) AS distance, typeID, itemName, itemID, typeName, solarSystemName, regionID, regionName FROM rena.mapAllCelestials WHERE solarSystemID = :solarSystemID ORDER BY distance ASC", array(":x" => $x, ":y" => $y, ":z" => $z, ":solarSystemID" => $solarSystemID));
        // Types
        $types = array("Stargate", "Moon", "Planet", "Asteroid Belt", "Sun");
        foreach ($types as $type) {
            if (isset($data["typeName"])) {
                if (strpos($data["typeName"], $type) !== false) {
                    $string = $type;
                    $string .= " (";
                    $string .= isset($data["itemName"]) ? $data["itemName"] : $data["solarSystemName"];
                    $string .= ")";
                    return $string;
                }
            }
        }
    }

    private function getDNA($itemData = array(), $shipTypeID)
    {

        // Get the container
        $container = getContainer();

        /** @var Db $db */
        $db = $container->get("db");

        $slots = array("LoSlot0", "LoSlot1", "LoSlot2", "LoSlot3", "LoSlot4", "LoSlot5", "LoSlot6", "LoSlot7", "MedSlot0", "MedSlot1", "MedSlot2", "MedSlot3", "MedSlot4", "MedSlot5", "MedSlot6", "MedSlot7", "HiSlot0", "HiSlot1", "HiSlot2", "HiSlot3", "HiSlot4", "HiSlot5", "HiSlot6", "HiSlot7", "DroneBay", "RigSlot0", "RigSlot1", "RigSlot2", "RigSlot3", "RigSlot4", "RigSlot5", "RigSlot6", "RigSlot7", "SubSystem0", "SubSystem1", "SubSystem2", "SubSystem3", "SubSystem4", "SubSystem5", "SubSystem6", "SubSystem7", "SpecializedFuelBay");
        $fittingArray = array();
        $fittingString = $shipTypeID . ":";
        foreach ($itemData as $item) {
            $flagName = $db->queryField("SELECT flagName FROM rena.invFlags WHERE flagID = :id", "flagName", array(":id" => $item["flag"]), 3600);
            if (in_array($flagName, $slots) || @$item["categoryID"] == 8) {
                if (isset($fittingArray[$item["typeID"]]))
                    $fittingArray[$item["typeID"]]["count"] = $fittingArray[$item["typeID"]]["count"] + (@$item["qtyDropped"] + @$item["qtyDestroyed"]);
                else
                    $fittingArray[$item["typeID"]] = array("count" => (@$item["qtyDropped"] + @$item["qtyDestroyed"]));
            }
        }
        foreach ($fittingArray as $key => $item)
            $fittingString .= "$key;" . $item["count"] . ":";
        $fittingString .= ":";
        return $fittingString;
    }

    private function calculateKillValue($killData)
    {
        if(empty($killData["items"]) || !isset($killData["items"]))
            return array("itemValue" => 0, "shipValue" => 0, "totalValue" => 0);

        $items = $killData["items"];
        $victimShipValue = $this->getPriceForTypeID($killData["victim"]["shipTypeID"], "avgSell", $killData["killTime"]);
        $killValue = 0;
        foreach ($items as $item) {
            $isCargo = isset($item["items"]) ? is_array($item["items"]) ? true : false : false;
            if ($isCargo)
                foreach ($item["items"] as $innerItem)
                    $killValue += $this->processItem($innerItem, $killData["killTime"], $isCargo);

            $killValue += $this->processItem($item, $killData["killTime"], $isCargo);
        }

        return array("itemValue" => $killValue, "shipValue" => $victimShipValue, "totalValue" => $killValue + $victimShipValue);
    }

    private function getPriceForTypeID($typeID, $type = "avgSell", $date = null)
    {
        // Get the container
        $container = getContainer();

        /** @var Db $db */
        $db = $container->get("db");

        $validTypes = array("avgSell", "avgBuy", "lowSell", "lowBuy", "highSell", "highBuy");
        if (!in_array($type, $validTypes))
            throw new \Exception("Type not valid, please select a valid type: " . implode(", ", $validTypes));

        $data = $db->queryField("SELECT {$type} FROM rena.invPrices WHERE typeID = :typeID ORDER BY created DESC LIMIT 1", $type, array(":typeID" => $typeID));

        if (!$data)
            return 0;

        return $data;
    }

    private function processItem($itemData, $killTime, $isCargo = false)
    {
        // Get the container
        $container = getContainer();

        /** @var typeIDs $typeIDs */
        $typeIDs = $container->get("typeIDs");

        $typeID = $itemData["typeID"];
        $flag = $itemData["flag"];
        $id = $typeIDs->getAllByTypeID($typeID)->toArray()[0];
        $itemName = $id["name"]["en"];

        if (!$itemName)
            $itemName = "TypeID {$typeID}";

        if ($typeID == 33329 && $flag == 89)
            $price = 0.01; // Golden pod
        else
            $price = $this->getPriceForTypeID($typeID, "avgSell", $killTime);

        if ($isCargo && strpos($itemName, "Blueprint") !== false)
            $itemData["singleton"] = 2;

        if ($itemData["singleton"] == 2)
            $price = $price / 100;

        return ($price * ($itemData["qtyDropped"] + $itemData["qtyDestroyed"]));
    }
}