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

class KillAPIController extends Controller
{
    private $killmails;
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->killmails = $this->mongo->selectCollection("thessia", "killmails");
    }

    public function addKill() {
        return array("error" => "not yet implemented");
    }

    public function getKillCount() {
        return $this->json(array("killCount" => $this->killmails->count()));
    }

    public function getKillByID(int $killID) {
        $md5 = md5($killID);
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));

        $data = $this->killmails->findOne(array("killID" => $killID), array("projection" => array("_id" => 0)));
        $data["killTime"] = date(DateTime::ISO8601, $data["killTime"]->__toString() / 1000);
        $this->cache->set($md5, $data, 3600);
        return $this->json($data);
    }

    public function getKillByHash(string $crestHash) {
        $md5 = md5($crestHash);
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));

        $data = $this->killmails->findOne(array("crestHash" => $crestHash), array("projection" => array("_id" => 0)));
        $data["killTime"] = date(DateTime::ISO8601, $data["killTime"]->__toString() / 1000);
        $this->cache->set($md5, $data, 3600);
        return $this->json($data);
    }

    public function getKillsByDate($timeStamp) {
        $md5 = md5($timeStamp);
        if($this->cache->exists($md5))
            return $this->json($this->cache->get($md5));

        $startDate = $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime($timeStamp)));
        $endDate = $this->makeTimeFromDateTime(date("Y-m-d H:i:s", strtotime($timeStamp) + 86400));

        $formatData = array();
        $data = $this->killmails->aggregate(array(
            array('$match' => array("killTime" => array("\$gte" => $startDate, "\$lt" => $endDate))),
            array('$project' => array("_id" => 0, "killID" => 1, "crestHash" => 1)),
            array('$sort' => array("killID" => -1))
        ))->toArray();

        foreach($data as $km)
            $formatData[$km["killID"]] = $km["crestHash"];

        $this->cache->set($md5, $data, 3600);
        return $this->json($formatData);
    }
}