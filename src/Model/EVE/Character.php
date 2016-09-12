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

use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;
use Thessia\Helper\EVEApi\EVE;

class Character {
    private $mongodb;
    private $collection;
    private $eve;

    public function __construct(Client $mongo, EVE $eve) {

        $this->mongodb = $mongo;
        $this->collection = $this->mongodb->selectCollection("thessia", "characters");
        $this->eve = $eve;
    }

    public function getCharacterInfo($characterID) {
        $sheet = $this->eve->eveCharacterInfo($characterID);

        $data = array();
        if(isset($sheet["result"]["characterName"])) {
            $data = array(
                "characterID" => (int) $sheet["result"]["characterID"],
                "characterName" => $sheet["result"]["characterName"],
                "corporationID" => (int) $sheet["result"]["corporationID"],
                "corporationName" => $sheet["result"]["corporation"],
                "corporationJoinDate" => new UTCDatetime(strtotime($sheet["result"]["corporationDate"]) * 1000),
                "allianceID" => (int) isset($sheet["result"]["allianceID"]) ? $sheet["result"]["allianceID"] : 0,
                "allianceName" => isset($sheet["result"]["alliance"]) ? $sheet["result"]["alliance"] : "",
                "allianceJoinDate" => isset($sheet["result"]["allianceDate"]) ? new UTCDatetime(strtotime($sheet["result"]["allianceDate"]) * 1000) : null,
                "securityStatus" => (float) $sheet["result"]["securityStatus"],
                "race" => $sheet["result"]["race"],
                "lastUpdated" => new UTCDatetime(time() * 1000),
                "history" => $sheet["result"]["employmentHistory"]
            );
        }

        return $data;
    }

}