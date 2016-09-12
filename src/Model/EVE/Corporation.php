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
use Thessia\Model\Database\EVE\Alliances;


class Corporation
{
    private $mongodb;
    private $collection;
    private $corporation;
    private $alliances;

    public function __construct(Client $mongo, \Thessia\Helper\EVEApi\Corporation $corporation, Alliances $alliances) {
        $this->mongodb = $mongo;
        $this->collection = $this->mongodb->selectCollection("thessia", "characters");
        $this->corporation = $corporation;
        $this->alliances = $alliances;
    }

    public function getCorporationInfo($corporationID) {
        $sheet = $this->corporation->corporationCorporationSheet(null, null, $corporationID);
        $data = array();
        if(isset($sheet["result"]["corporationName"])) {
            $data = array(
                "corporationID" => (int) $sheet["result"]["corporationID"],
                "corporationName" => $sheet["result"]["corporationName"],
                "allianceID" => (int) isset($sheet["result"]["allianceID"]) ? $sheet["result"]["allianceID"] : 0,
                "allianceName" => isset($sheet["result"]["allianceID"]) ? $this->alliances->getAllByID($sheet["result"]["allianceID"])["allianceName"] : "",
                "ceoID" => (int) $sheet["result"]["ceoID"],
                "ticker" => $sheet["result"]["ticker"],
                "memberCount" => (int) $sheet["result"]["memberCount"],
                "lastUpdated" => new UTCDatetime(time() * 1000),
                "information" => $sheet
            );
        }

        return $data;
    }
}