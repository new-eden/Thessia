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
        return $this->json($this->killmails->findOne(array("killID" => $killID)));
    }

    public function getKillByHash(string $crestHash) {
        return $this->json($this->killmails->findOne(array("crestHash" => $crestHash)));
    }

    public function getKillsByDate($timeStamp) {
        $startDate = $this->makeTimeFromDateTime(date("Y-m-d", strtotime($timeStamp)));
        $endDate = $this->makeTimeFromDateTime(date("Y-m-d", strtotime($timeStamp) + 86400));

        $formatData = array();
        $data = $this->killmails->find(
            array("killTime" => array("\$gte" => $startDate, "\$lte" => $endDate)),
            array(
                "projection" => array("_id" => 0, "killID" => 1, "crestHash" => 1),
                "sort" => array("killID" => -1)
            )
        )->toArray();

        foreach($data as $km)
            $formatData[$km["killID"]] = $km["crestHash"];

        return $this->json($formatData);
    }
}