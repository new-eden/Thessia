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

namespace Thessia\Controller\API;

use Slim\App;
use Thessia\Middleware\Controller;

class LossesAPIController extends Controller
{
    private $participants;
    public $validArguments = array(
        "killTime" => "datetime",
        "solarSystemID" => "int",
        "regionID" => "int",
        "shipValue" => "float",
        "fittingValue" => "float",
        "totalValue" => "float",
        "isNPC" => "bool",
        "isSolo" => "bool",
        "victim.shipTypeID" => "int",
        "victim.characterID" => "int",
        "victim.corporationID" => "int",
        "victim.allianceID" => "int",
        "victim.factionID" => "int",
        "attackers.shipTypeID" => "int",
        "attackers.weaponTypeID" => "int",
        "attackers.characterID" => "int",
        "attackers.corporationID" => "int",
        "attackers.allianceID" => "int",
        "attackers.factionID" => "int",
        "attackers.finalBlow" => "int",
        "items.typeID" => "int",
        "items.groupID" => "int",
        "items.categoryID" => "int",
        "page" => "int",
        "limit" => "int",
        "order" => "string",
    );

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->participants = $this->container->get("participants");
    }

    private function verifyParams($extraParameters) {
        $validArguments = $this->validArguments;
        $arguments = explode("/", rtrim($extraParameters, "/"));

        $count = 0;
        $tempArray = array();
        $returnArray = array();
        foreach($arguments as $param) {
            if(empty($param))
                continue;

            if($count % 2 == false)
                $tempArray[$param] = $arguments[$count + 1];

            $count++;
        }

        foreach($tempArray as $key => $value) {
            foreach($validArguments as $arg => $type) {
                if ($key == $arg) {
                    switch ($type) {
                        case "int":
                            $returnArray[$key] = (int)$value;
                            break;

                        case "string":
                            $returnArray[$key] = (string)$value;
                            break;

                        case "float":
                            $returnArray[$key] = (float)$value;
                            break;

                        case "bool":
                            $returnArray[$key] = (bool)$value;
                            break;
                        case "datetime":
                            if (is_numeric($value))
                                $returnArray[$key] = (int)$value * 1000;
                            else
                                $returnArray[$key] = (int)strtotime($value * 1000);

                            break;
                    }
                }
            }
        }

        // Do validation (This is about as ugly as it gets ...
        $returnArray["page"] = isset($returnArray["page"]) ? $returnArray["page"] : 1;
        $returnArray["limit"] = isset($returnArray["limit"]) ? $returnArray["limit"] : 100;
        $returnArray["offset"] = isset($returnArray["offset"]) ? $returnArray["offset"] : 0;
        $returnArray["order"] = isset($returnArray["order"]) ? $returnArray["order"] : "DESC";

        if($returnArray["page"] > 1)
            $returnArray["offset"] = $returnArray["limit"] * $returnArray["page"];

        if($returnArray["limit"] > 100)
            $returnArray["limit"] = 100;

        if($returnArray["limit"] < 1)
            $returnArray["limit"] = 1;

        $validOrder = array("ASC", "DESC");
        if(!in_array($returnArray["order"], $validOrder))
            $returnArray["order"] = "DESC";

        return $returnArray;
    }

    public function getCharacter(int $characterID, $extraParameters = null) {
        $params = $this->verifyParams($extraParameters);
        return $this->json($this->participants->getByVictimCharacterID($characterID, $params, $params["limit"], 360, $params["order"], $params["offset"]));
    }

    public function getCorporation(int $corporationID, $extraParameters = null) {
        $params = $this->verifyParams($extraParameters);
        return $this->json($this->participants->getByVictimCorporationID($corporationID, $params, $params["limit"], 360, $params["order"], $params["offset"]));
    }

    public function getAlliance(int $allianceID, $extraParameters = null) {
        $params = $this->verifyParams($extraParameters);
        return $this->json($this->participants->getByVictimAllianceID($allianceID, $params, $params["limit"], 360, $params["order"], $params["offset"]));
    }

    public function getFaction(int $factionID, $extraParameters = null) {
        $params = $this->verifyParams($extraParameters);
        return $this->json($this->participants->getByVictimFactionID($factionID, $params, $params["limit"], 360, $params["order"], $params["offset"]));
    }

    public function getShipType(int $shipTypeID, $extraParameters = null) {
        $params = $this->verifyParams($extraParameters);
        return $this->json($this->participants->getByVictimShipTypeID($shipTypeID, $params, $params["limit"], 360, $params["order"], $params["offset"]));
    }
}