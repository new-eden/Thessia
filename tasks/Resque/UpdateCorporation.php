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

namespace Thessia\Tasks\Resque;


use League\Container\Container;
use MongoDB\BSON\UTCDatetime;
use MongoDB\Client;
use MongoDB\Collection;
use Thessia\Helper\EVEApi\Corporation;
use Thessia\Model\Database\EVE\Alliances;
use Thessia\Model\EVE\Parser;

class UpdateCorporation
{
    /**
     * @var Container
     */
    private $container;

    public function perform()
    {
        /** @var Client $mongodb */
        $mongodb = $this->container->get("mongo");
        /** @var Collection $collection */
        $collection = $mongodb->selectCollection("thessia", "corporations");
        /** @var Corporation $corporation */
        $corporation = $this->container->get("ccpCorporation");
        /** @var Alliances $alliance */
        $alliance = $this->container->get("alliances");
        $corporationID = (int) $this->args["corporationID"];

        if($corporationID == 0)
            exit;

        $sheet = $corporation->corporationCorporationSheet(null, null, $corporationID);
        $data = array(
            "corporationID" => $sheet["result"]["corporationID"],
            "corporationName" => $sheet["result"]["corporationName"],
            "allianceID" => $sheet["result"]["allianceID"],
            "allianceName" => $alliance->getAllByID($sheet["result"]["allianceID"])["allianceName"],
            "ceoID" => $sheet["result"]["ceoID"],
            "ticker" => $sheet["result"]["ticker"],
            "memberCount" => $sheet["result"]["memberCount"],
            "lastUpdated" => new UTCDatetime(time() * 1000),
            "information" => $sheet
        );

        $collection->updateOne(array("corporationID" => $corporationID), $data);
        exit();
    }

    public function setUp()
    {
        $this->container = getContainer();
    }

    public function tearDown()
    {

    }
}