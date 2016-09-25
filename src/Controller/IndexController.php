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

namespace Thessia\Controller;

use Thessia\Middleware\Controller;

class IndexController extends Controller
{
    public function index($page = 1)
    {
        //@todo add information from the login data, if the person is logged in tha is..
        if($page == 1) {
            $menu = array(
                "Navigation" => array(
                    "Next Page" => $this->getFullHost() . "page/" . ($page + 1) . "/",
                )
            );
        } else {
            $menu = array(
                "Navigation" => array(
                    "Previous Page" => $this->getFullHost() . "page/" . ($page - 1) . "/",
                    "Next Page" => $this->getFullHost() . "page/" . ($page + 1) . "/",
                )
            );
        }

        return $this->render("/pages/frontpage.twig", array("menu" => $menu));
    }

    private function getLoginData() {

    }

    public function kill($killID) {
        // Figure out if there is anything related in this system
        $collection = $this->mongo->selectCollection("thessia", "killmails");
        $data = $collection->findOne(array("killID" => (int)$killID));
        $victimID = $data["victim"]["characterID"];
        $victimName = $data["victim"]["characterName"];
        $fbID = 0;
        $fbName = "";
        $victimShipName = $data["victim"]["shipTypeName"];
        $victimShipID = $data["victim"]["shipTypeID"];
        $tdID = 0;
        $solarSystemName = $data["solarSystemName"];
        $solarSystemID = $data["solarSystemID"];
        $regionID = $data["regionID"];
        $regionName = $data["regionName"];
        $tdName = "";
        $dna = $data["dna"];

        foreach($data["attackers"] as $key => $attacker) {
            if($key == 0) {
                $tdID = $attacker["characterID"];
                $tdName = $attacker["characterName"];
            }
            if($attacker["finalBlow"] == 1) {
                $fbID = $attacker["characterID"];
                $fbName = $attacker["characterName"];
            }
        }

        $md5 = md5("relatedCheck{$killID}");
        if($this->cache->exists($md5) == false) {
            $killTime = $data["killTime"]->__toString() / 1000;
            $systemID = $data["solarSystemID"];
            $date1 = $killTime - 600;
            $date2 = $killTime + 600;
            $related = $collection->find(array("solarSystemID" => $systemID, "killTime" => array('$gte' => $this->makeTimeFromUnixTime($date1), '$lte' => $this->makeTimeFromUnixTime($date2))));
            $this->cache->set($md5, $related);
        } else {
            $related = $this->cache->get($md5);
        }

        // Create Menu
        $menu = array(
            "Killmail" => "https://crest.eveonline.com/killmails/{$data["killID"]}/{$data["crestHash"]}/",
        );
        if(count($related) > 1)
            $menu["Related Kills"] = "/related/{$killID}/";

        $menu["Osmium"] = array(
            $victimShipName . "fit" => "https://o.smium.org/loadout/dna/{$dna}"
        );
        $menu["EVEGate"] = array(
            $victimName => "https://gate.eveonline.com/Profile/{$victimName}",
            $fbName => "https://gate.eveonline.com/Profile/{$fbName}",
            $tdName => "https://gate.eveonline.com/Profile/{$tdName}",
        );
        $menu["EVEWho"] = array(
            $victimName => "http://evewho.com/pilot/{$victimName}",
            $fbName => "http://evewho.com/pilot/{$fbName}",
            $tdName => "http://evewho.com/pilot/{$tdName}",
        );
        $menu["Dotlan"] = array(
            $solarSystemName => "http://evemaps.dotlan.net/system/{$solarSystemName}",
            $regionName => "http://evemaps.dotlan.net/region/{$regionName}",
        );

        return $this->render("/pages/kill.twig", array("killID" => $killID, "menu" => $menu, "related" => count($related) > 0 ? true : false));
    }

    public function about() {
        return $this->render("/pages/about.twig", array());
    }
}