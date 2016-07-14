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

namespace Thessia\Model\EVE;


use MongoDB\BSON\UTCDatetime;
use MongoDB\Client;
use Thessia\Lib\Cache;
use Thessia\Lib\cURL;
use Thessia\Model\CCP\groupIDs;
use Thessia\Model\CCP\solarSystems;
use Thessia\Model\CCP\typeIDs;

/**
 * Class Parser
 * @package Thessia\Model\EVE
 */
class Parser {
    /**
     * @var typeIDs
     */
    private $typeIDs;
    /**
     * @var solarSystems
     */
    private $solarSystems;
    /**
     * @var Prices
     */
    private $prices;
    /**
     * @var Killmails
     */
    private $killmails;
    /**
     * @var Alliances
     */
    private $alliances;
    /**
     * @var Corporations
     */
    private $corporations;
    /**
     * @var Characters
     */
    private $characters;
    /**
     * @var groupIDs
     */
    private $groupIDs;
    /**
     * @var Crest
     */
    private $crest;
    /**
     * @var cURL
     */
    private $curl;
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var string
     */
    private $imageServer;
    /**
     * @var Client
     */
    private $mongo;

    /**
     * Parser constructor.
     * @param typeIDs $typeIDs
     * @param solarSystems $solarSystems
     * @param Prices $prices
     * @param Killmails $killmails
     * @param Alliances $alliances
     * @param Corporations $corporations
     * @param Characters $characters
     * @param groupIDs $groupIDs
     * @param Crest $crest
     * @param cURL $cURL
     * @param Cache $cache
     * @param Client $mongo
     */
    public function __construct(typeIDs $typeIDs, solarSystems $solarSystems, Prices $prices, Killmails $killmails, Alliances $alliances, Corporations $corporations, Characters $characters, groupIDs $groupIDs, Crest $crest, cURL $cURL, Cache $cache, Client $mongo) {
        $this->typeIDs = $typeIDs;
        $this->solarSystems = $solarSystems;
        $this->prices = $prices;
        $this->killmails = $killmails;
        $this->alliances = $alliances;
        $this->corporations = $corporations;
        $this->characters = $characters;
        $this->groupIDs = $groupIDs;
        $this->crest = $crest;
        $this->curl = $cURL;
        $this->cache = $cache;
        $this->mongo = $mongo;
        $this->imageServer = "https://image.eveonline.com/";
    }

    /**
     * Parses data from CREST into a format the parser can process
     *
     * @param $killID
     * @param $killHash
     * @return array
     */
    public function parseCrestKillmail($killID, $killHash) {
        $url = "https://crest.eveonline.com/killmails/{$killID}/{$killHash}/";
        $data = json_decode($this->curl->getData($url), true);

        // Generate the mail from CREST data
        $killmail = $this->crest->generateFromCREST(array("killID" => $killID, "killmail" => $data));

        // Parse the killmail data and return it..
        return $this->parseKillmail($killmail, $killHash);
    }

    /**
     * Parses data from the XML API into a format the parser can process
     *
     * @param $xmlMail
     */
    public function parseXMLKillmail($xmlMail) {

    }

    /**
     * Parses and processes the killmail data to the format stored in MongoDB
     *
     * @param $killmailData
     * @param $killHash
     * @return array
     */
    private function parseKillmail(array $killmailData, string $killHash): array {
        $killmail = array();

        $killmail["killID"] = (int)$killmailData["killID"];
        $unixTime = strtotime($killmailData["killTime"]) * 1000;
        $killmail["killTime"] = new UTCDatetime($unixTime);

        // Generate the top portion of the mail
        $killmail = array_merge($killmail, $this->generateTopPortion($killmailData, $killHash));

        // Generate the victim portion of the mail
        $killmail["victim"] = $this->generateVictimPortion($killmailData["victim"]);

        // Generate the attackers portion of the mail
        $killmail["attackers"] = $this->generateAttackersPortion($killmailData["attackers"]);

        // Generate the Item portion of the mail
        $killmail["items"] = $this->generateItemPortion($killmailData["items"]);

        // Get the Osmium data, for the Osmium portion of the mail
        $killmail["osmium"] = $this->generateOsmiumPortion($killmail["dna"]);

        return $killmail;
    }

    /**
     * Generate the top portion of the mail array
     *
     * @param $data
     * @param string $killHash
     * @return array
     */
    private function generateTopPortion($data, $killHash): array {
        $top = array();

        $top["solarSystemID"] = (int)$data["solarSystemID"];
        $solarData = $this->solarSystems->getAllBySolarSystemID($data["solarSystemID"])->toArray()[0];
        $top["solarSystemName"] = $solarData["solarSystemName"];
        $top["regionID"] = (int)$solarData["regionID"];
        $top["regionName"] = $solarData["regionName"];
        $top["near"] = $this->getNear($data["victim"]["x"], $data["victim"]["y"], $data["victim"]["z"], $data["solarSystemID"]);
        $top["x"] = (float)$data["victim"]["x"];
        $top["y"] = (float)$data["victim"]["y"];
        $top["z"] = (float)$data["victim"]["z"];
        $top["moonID"] = (int)$data["moonID"];
        $killValues = $this->calculateKillValue($data);
        $top["shipValue"] = (float)$killValues["shipValue"];
        $top["fittingValue"] = (float)$killValues["itemValue"];
        $top["totalValue"] = (float)$killValues["totalValue"];
        $top["dna"] = $this->getDNA($data["items"], $data["victim"]["shipTypeID"]);
        $top["crestHash"] = $killHash;
        $top["isNPC"] = $this->isNPC($data);
        $top["isSolo"] = $this->isSolo($data);

        return $top;
    }

    /**
     * Generate the victim portion of the mail array
     *
     * @param $data
     * @return array
     */
    private function generateVictimPortion($data): array {
        $victim = array();

        $victim["x"] = (float)$data["x"];
        $victim["y"] = (float)$data["y"];
        $victim["z"] = (float)$data["z"];
        $victim["shipTypeID"] = (int)$data["shipTypeID"];
        $shipData = $this->typeIDs->getAllByTypeID($data["shipTypeID"])->toArray()[0];
        $victim["shipTypeName"] = $shipData["name"]["en"];
        $victim["shipImageURL"] = $this->imageServer . "Type/" . $data["shipTypeID"] . "_32.png";
        $victim["damageTaken"] = (int)$data["damageTaken"];
        $victim["characterID"] = (int)$data["characterID"];
        $victim["characterName"] = $data["characterName"];
        $victim["characterImageURL"] = $this->imageServer . "Character/" . $data["characterID"] . "_128.jpg";
        $victim["corporationID"] = (int)$data["corporationID"];
        $victim["corporationName"] = $data["corporationName"];
        $victim["corporationImageURL"] = $this->imageServer . "Corporation/" . $data["corporationID"] . "_128.png";
        $victim["allianceID"] = (int)$data["allianceID"];
        $victim["allianceName"] = $data["allianceName"];
        $victim["allianceImageURL"] = $this->imageServer . "Alliance/" . $data["allianceID"] . "_128.png";
        $victim["factionID"] = (int)$data["factionID"];
        $victim["factionName"] = $data["factionName"];
        $victim["factionImageURL"] = $this->imageServer . "Alliance/" . $data["factionID"] . "_128.png";

        return $victim;
    }

    /**
     * Generate the attackers portion of the mail array
     *
     * @param $data
     * @return mixed
     */
    private function generateAttackersPortion($data): array {
        $attackers = array();

        foreach ($data as $attacker) {
            $inner = array();
            $inner["characterID"] = (int)$attacker["characterID"];
            $inner["characterName"] = $attacker["characterName"];
            $inner["characterImageURL"] = $this->imageServer . "Character/" . $attacker["characterID"] . "_128.jpg";
            $inner["corporationID"] = (int)$attacker["corporationID"];
            $inner["corporationName"] = $attacker["corporationName"];
            $inner["corporationImageURL"] = $this->imageServer . "Corporation/" . $attacker["corporationID"] . "_128.png";
            $inner["allianceID"] = (int)$attacker["allianceID"];
            $inner["allianceName"] = $attacker["allianceName"];
            $inner["allianceImageURL"] = $this->imageServer . "Alliance/" . $attacker["allianceID"] . "_128.png";
            $inner["factionID"] = (int)$attacker["factionID"];
            $inner["factionName"] = $attacker["factionName"];
            $inner["factionImageURL"] = $this->imageServer . "Alliance/" . $attacker["factionID"] . "_128.png";
            $inner["securityStatus"] = (float)$attacker["securityStatus"];
            $inner["damageDone"] = (int)$attacker["damageDone"];
            $inner["finalBlow"] = (int)$attacker["finalBlow"];
            $inner["weaponTypeID"] = (int)$attacker["weaponTypeID"];
            if ($attacker["weaponTypeID"] > 0) {
                $weaponData = $this->typeIDs->getAllByTypeID($attacker["weaponTypeID"])->toArray()[0];
                $inner["weaponTypeName"] = $weaponData["name"]["en"];
            } else {
                $inner["weaponTypeName"] = "";
            }
            $inner["weaponImageURL"] = $this->imageServer . "Type/" . $attacker["weaponTypeID"] . "_32.png";
            $inner["shipTypeID"] = (int)$attacker["shipTypeID"];
            $shipData = $this->typeIDs->getAllByTypeID($attacker["shipTypeID"])->toArray()[0];
            $inner["shipTypeName"] = $shipData["name"]["en"];
            $inner["shipImageURL"] = $this->imageServer . "Type/" . $attacker["shipTypeID"] . "_32.png";

            $attackers[] = $inner;
        }

        return $attackers;
    }

    /**
     * Generate the item portion of the mail array
     *
     * @param $data
     * @return array
     * @throws \Exception
     */
    private function generateItemPortion($data): array {
        $items = array();

        foreach ($data as $item) {
            $inner = array();
            $inner["typeID"] = (int)$item["typeID"];
            $typeData = $this->typeIDs->getAllByTypeID($item["typeID"])->toArray()[0];
            $inner["typeName"] = $typeData["name"]["en"];
            $inner["typeImageURL"] = $this->imageServer . "Type/" . $item["typeID"] . "_32.png";
            $inner["groupID"] = $typeData["groupID"];
            $inner["categoryID"] = $this->groupIDs->getAllByGroupID($inner["groupID"])->toArray()[0]["categoryID"];
            $inner["flag"] = (int)$item["flag"];
            $inner["qtyDropped"] = (int)$item["qtyDropped"];
            $inner["qtyDestroyed"] = (int)$item["qtyDestroyed"];
            $inner["singleton"] = (int)$item["singleton"];
            $inner["value"] = (float)$this->getPriceForTypeID($item["typeID"]);

            $items[] = $inner;
        }

        return $items;
    }

    /**
     * Get the fitting data from O.smium for the fit on the ship that blew up.
     *
     * @param $data
     * @return array|mixed
     */
    private function generateOsmiumPortion($data): array {
        $data = $this->curl->getData("https://o.smium.org/api/json/loadout/dna/attributes/loc:ship,a:tank,a:ehpAndResonances,a:capacitors,a:damage?input={$data}");
        if (is_array($data))
            return array();
        return json_decode($data, true);
    }

    /**
     * @param $x
     * @param $y
     * @param $z
     * @param $solarSystemID
     * @return string
     * @throws \Exception
     */
    private function getNear($x, $y, $z, $solarSystemID): string {
        if ($x == 0 && $y == 0 && $z == 0)
            return "";

        $collection = $this->mongo->selectCollection("ccp", "celestials");
        $celestials = $collection->find(array("solarSystemID" => $solarSystemID));
        $minimumDistance = null;
        $celestialName = "";

        foreach ($celestials as $celestial) {
            $distance = sqrt(pow($celestial["x"] - $x, 2) + pow($celestial["y"] - $y, 2) + pow($celestial["z"] - $z, 2));

            if ($minimumDistance === null) {
                $minimumDistance = $distance;
                $celestialName = $this->fillInCelestialName($celestial);
            } elseif ($distance >= $minimumDistance) {
                $minimumDistance = $distance;
                $celestialName = $this->fillInCelestialName($celestial);
            }
        }

        return $celestialName;
    }

    /**
     * @param $celestial
     * @return string
     */
    private function fillInCelestialName($celestial): string {
        $celestialName = "";
        $types = array("Stargate", "Moon", "Planet", "Asteroid Belt", "Sun");
        foreach ($types as $type) {
            if (isset($celestial["typeName"])) {
                if (strpos($celestial["typeName"], $type) !== false) {
                    $string = $type;
                    $string .= " (";
                    $string .= isset($celestial["itemName"]) ? $celestial["itemName"] : $celestial["solarSystemName"];
                    $string .= ")";
                    $celestialName = $string;
                }
            }
        }

        return $celestialName;
    }

    /**
     * @param array $itemData
     * @param $shipTypeID
     * @return string
     * @throws \Exception
     */
    private function getDNA($itemData = array(), $shipTypeID): string {
        $collection = $this->mongo->selectCollection("ccp", "invFlags");

        $slots = array("LoSlot0", "LoSlot1", "LoSlot2", "LoSlot3", "LoSlot4", "LoSlot5", "LoSlot6", "LoSlot7", "MedSlot0", "MedSlot1", "MedSlot2", "MedSlot3", "MedSlot4", "MedSlot5", "MedSlot6", "MedSlot7", "HiSlot0", "HiSlot1", "HiSlot2", "HiSlot3", "HiSlot4", "HiSlot5", "HiSlot6", "HiSlot7", "DroneBay", "RigSlot0", "RigSlot1", "RigSlot2", "RigSlot3", "RigSlot4", "RigSlot5", "RigSlot6", "RigSlot7", "SubSystem0", "SubSystem1", "SubSystem2", "SubSystem3", "SubSystem4", "SubSystem5", "SubSystem6", "SubSystem7", "SpecializedFuelBay");
        $fittingArray = array();
        $fittingString = $shipTypeID . ":";
        foreach ($itemData as $item) {
            $flagName = $collection->findOne(array("flagID" => $item["flag"]))["flagName"];
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

    /**
     * @param $killData
     * @return array
     * @throws \Exception
     */
    private function calculateKillValue($killData): array {
        if (empty($killData["items"]) || !isset($killData["items"]))
            return array("itemValue" => 0, "shipValue" => 0, "totalValue" => 0);

        $items = $killData["items"];
        $victimShipValue = $this->getPriceForTypeID($killData["victim"]["shipTypeID"]);
        $killValue = 0;
        foreach ($items as $item) {
            $isCargo = isset($item["items"]) ? is_array($item["items"]) ? true : false : false;
            if ($isCargo)
                foreach ($item["items"] as $innerItem)
                    $killValue += $this->processItem($innerItem, $isCargo);

            $killValue += $this->processItem($item, $isCargo);
        }

        return array("itemValue" => $killValue, "shipValue" => $victimShipValue, "totalValue" => $killValue + $victimShipValue);
    }

    /**
     * @param $typeID
     * @return integer
     */
    private function getPriceForTypeID($typeID): int {
        $data = $this->prices->getPriceForTypeID($typeID);
        $value = $data["averagePrice"];

        if (!$value)
            return 0;
        return $value;
    }

    /**
     * @param $itemData
     * @param bool $isCargo
     * @return double
     * @throws \Exception
     */
    private function processItem($itemData, $isCargo = false): float {
        $typeID = $itemData["typeID"];
        $flag = $itemData["flag"];
        $id = $this->typeIDs->getAllByTypeID($typeID)->toArray()[0];
        $itemName = $id["name"]["en"];

        if (!$itemName)
            $itemName = "TypeID {$typeID}";

        if ($typeID == 33329 && $flag == 89) // Golden pod
            $price = 0.01;
        else
            $price = $this->getPriceForTypeID($typeID);

        if ($isCargo && strpos($itemName, "Blueprint") !== false)
            $itemData["singleton"] = 2;

        if ($itemData["singleton"] == 2)
            $price = $price / 100;

        return ($price * ($itemData["qtyDropped"] + $itemData["qtyDestroyed"]));
    }

    /**
     * If there is only an NPC (or multiple NPCs) on the mail, it's an NPC mail.
     *
     * @param $killData
     * @return bool
     */
    private function isNPC($killData): bool {
        $npc = 0;
        $calc = 0;
        $kdCount = count($killData["attackers"]);

        foreach ($killData["attackers"] as $attacker)
            $npc += $attacker["characterID"] == 0 && ($attacker["corporationID"] < 1999999 && $attacker["corporationID"] != 1000125) ? 1 : 0;

        if ($kdCount > 0 && $npc > 0)
            $calc = count($killData["attackers"]) / $npc;

        if ($calc == 1)
            return true;
        return false;
    }

    /**
     * If there is only one person on the mail (Excluding NPCs) then it's a solo mail
     * Altho, max allowed is 2 attackers. So if there is 5 attackers, 4 npc's and 1 character, it doesn't count as solo.
     *
     * @param $killData
     * @return bool
     */
    private function isSolo($killData): bool {
        $npc = 0;
        $calc = 0;
        $kdCount = count($killData["attackers"]);

        if ($kdCount > 2)
            return false;
        elseif ($kdCount == 1)
            return true;

        // Now to figure out if one of them is an npc
        foreach ($killData["attackers"] as $attacker)
            $npc += $attacker["characterID"] == 0 && ($attacker["corporationID"] < 1999999 && $attacker["corporationID"] != 1000125) ? 1 : 0;

        if ($npc > 0)
            $calc = 2 / $npc;

        // If there is one NPC, then calc is 1, and 2 divided by 1 is 2. So if the result is 2, then it's a solo mail with an npc on it.
        if ($calc == 2)
            return true;
        return false;
    }
}