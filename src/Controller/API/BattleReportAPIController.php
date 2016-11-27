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

use DateTime;
use Slim\App;
use Thessia\Middleware\Controller;

class BattleReportAPIController extends Controller
{
    private $collection;
    /**
     * AllianceAPIController constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $mongo = $this->container->get("mongo");
        $this->collection = $mongo->selectCollection("thessia", "battles");
    }

    public function getBattle($battleID) {
        $data = $this->collection->findOne(array("battleID" => $battleID), array("projection" => array("_id" => 0)));

        if(!empty($data)) {
            $data["killCount"] = count($data["teamRed"]["kills"]) + count($data["teamBlue"]["kills"]);
            $data["startTime"] = date(DateTime::ISO8601, $data["startTime"]->__toString() / 1000);
            $data["endTime"] = date(DateTime::ISO8601, $data["endTime"]->__toString() / 1000);
        }
        return $this->json($data);
    }

    public function getBattles($page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);
        $battles = array();
        $data = $this->collection->find(array(), array("sort" => array("startTime" => -1), "projection" => array("_id" => 0, "killData" => 0), "limit" => $limit, "skip" => $offset))->toArray();
        foreach($data as $d) {
            $tmp = array();
            $tmp["battleID"] = $d["battleID"];
            $tmp["startTime"] = date(DateTime::ISO8601, $d["startTime"]->__toString() / 1000);
            $tmp["endTime"] = date(DateTime::ISO8601, $d["endTime"]->__toString() / 1000);
            $tmp["solarSystemID"] = $d["solarSystemInfo"]["solarSystemID"];
            $tmp["solarSystemName"] = $d["solarSystemInfo"]["solarSystemName"];
            $tmp["regionID"] = $d["solarSystemInfo"]["regionID"];
            $tmp["regionName"] = $d["solarSystemInfo"]["regionName"];
            $tmp["killCount"] = count($d["teamBlue"]["kills"]) + count($d["teamRed"]["kills"]);
            $tmp["involvedCount"] = array(
                "characters" => count($d["teamRed"]["characters"]) + count($d["teamBlue"]["characters"]),
                "corporations" => count($d["teamRed"]["corporations"]) + count($d["teamBlue"]["corporations"]),
                "alliances" => count($d["teamRed"]["alliances"]) + count($d["teamBlue"]["alliances"]),
            );
            if(!empty($d["teamRed"]["alliances"]) && !empty($d["teamBlue"]["alliances"])) {
                $tmp["involvedAlliances"] = array(
                    "teamRed" => $d["teamRed"]["alliances"],
                    "teamBlue" => $d["teamBlue"]["alliances"],
                );
            } elseif(!empty($d["teamRed"]["corporations"]) && !empty($d["teamBlue"]["corporations"])) {
                $tmp["involvedCorporations"] = array(
                    "teamRed" => $d["teamRed"]["corporations"],
                    "teamBlue" => $d["teamBlue"]["corporations"],
                );
            }

            $battles[] = $tmp;
        }
        return $this->json($battles);
    }
}