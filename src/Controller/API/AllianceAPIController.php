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

use Thessia\Middleware\Controller;
use Slim\App;

class AllianceAPIController extends Controller
{
    /**
     * @var \MongoDB\Collection
     */
    private $collection;
    /**
     * @var \MongoDB\Collection
     */
    private $killmails;
    /**
     * @var \MongoDB\Collection
     */
    private $characters;
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->collection = $this->mongo->selectCollection("thessia", "alliances");
        $this->characters = $this->mongo->selectCollection("thessia", "characters");
        $this->killmails = $this->mongo->selectCollection("thessia", "killmails");
    }

    public function allianceCount() {
        $count = $this->collection->count();

        return $this->json(array("allianceCount" => $count));
    }

    public function allianceInformation(int $allianceID) {
        $info = $this->collection->find(array("allianceID" => $allianceID))->toArray();

        return $this->json($info);
    }

    public function findCorporation(string $searchTerm) {
        $find = $this->collection->find(array("\$text" => array("\$search" => "\"$searchTerm\"")), array("score" => array("\$meta" => "textScore"), "sort" => array("\$score" => -1), "limit" => 50))->toArray();
        return $this->json($find);
    }

    public function allianceMembers(int $allianceID) {
        $find = $this->characters->find(array("allianceID" => $allianceID))->toArray();
        return $this->json($find);
    }

    public function topCharacters(int $allianceID, int $limit = 10) {
        $data = $this->killmails->aggregate(array(
            array('$match' => array("victim.allianceID" => $allianceID)),
            array('$group' => array("_id" => '$victim.characterID', "count" => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "characterID" => '$_id')),
            array('$sort' => array("count" => -1)),
            array('$limit' => $limit)
        ))->toArray();

        return $this->json($data);
    }

    public function topCorporations(int $corporationID, int $limit = 10) {
        $data = $this->killmails->aggregate(array(
            array('$match' => array("victim.corporationID" => $corporationID)),
            array('$group' => array("_id" => '$victim.corporationID', "count" => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "corporationID" => '$_id')),
            array('$sort' => array("count" => -1)),
            array('$limit' => $limit)
        ))->toArray();

        return $this->json($data);
    }

    public function topAlliances(int $allianceID, int $limit = 10) {
        $data = $this->killmails->aggregate(array(
            array('$match' => array("victim.allianceID" => $allianceID)),
            array('$group' => array("_id" => '$victim.allianceID', "count" => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "allianceID" => '$_id')),
            array('$sort' => array("count" => -1)),
            array('$limit' => $limit)
        ))->toArray();

        return $this->json($data);
    }

    public function topShips(int $allianceID, int $limit = 10) {
        $data = $this->killmails->aggregate(array(
            array('$match' => array("victim.allianceID" => $allianceID)),
            array('$group' => array("_id" => '$victim.shipTypeID', "count" => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "shipTypeID" => '$_id')),
            array('$sort' => array("count" => -1)),
            array('$limit' => $limit)
        ))->toArray();

        return $this->json($data);
    }

    public function topSystems(int $allianceID, int $limit = 10) {
        $data = $this->killmails->aggregate(array(
            array('$match' => array("victim.allianceID" => $allianceID)),
            array('$group' => array("_id" => '$solarSystemID', "count" => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "solarSystemID" => '$_id')),
            array('$sort' => array("count" => -1)),
            array('$limit' => $limit)
        ))->toArray();

        return $this->json($data);
    }

    public function topRegions(int $allianceID, int $limit = 10) {
        $data = $this->killmails->aggregate(array(
            array('$match' => array("victim.allianceID" => $allianceID)),
            array('$group' => array("_id" => '$regionID', "count" => array('$sum' => 1))),
            array('$project' => array("_id" => 0, "count" => '$count', "regionID" => '$_id')),
            array('$sort' => array("count" => -1)),
            array('$limit' => $limit)
        ))->toArray();

        return $this->json($data);
    }
}