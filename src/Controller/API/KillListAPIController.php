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

/**
 * Created by PhpStorm.
 * User: micha
 * Date: 10-08-2016
 * Time: 11:59
 */

namespace Thessia\Controller\API;


use Slim\App;
use Thessia\Middleware\Controller;

class KillListAPIController extends Controller {
    private $killList;

    /**
     * ItemAPIController constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->killList = $this->container->get("killlist");
    }

    public function getLatest($page = 1) {
        return $this->json($this->killList->getLatest($page));
    }

    public function getBigKills($page = 1) {
        return $this->json($this->killList->getBigKills($page));
    }

    public function getWSpace($page = 1) {
        return $this->json($this->killList->getWSpace($page));
    }

    public function getHighSec($page = 1) {
        return $this->json($this->killList->getHighSec($page));
    }

    public function getLowSec($page = 1) {
        return $this->json($this->killList->getLowSec($page));
    }

    public function getNullSec($page = 1) {
        return $this->json($this->killList->getNullSec($page));
    }

    public function getSolo($page = 1) {
        return $this->json($this->killList->getSolo($page));
    }

    public function getNPC($page = 1) {
        return $this->json($this->killList->getNPC($page));
    }

    public function get5b($page = 1) {
        return $this->json($this->killList->get5b($page));
    }

    public function get10b($page = 1) {
        return $this->json($this->killList->get10b($page));
    }

    public function getCitadels($page = 1) {
        return $this->json($this->killList->getCitadels($page));
    }

    public function getT1($page = 1) {
        return $this->json($this->killList->getT1($page));
    }

    public function getT2($page = 1) {
        return $this->json($this->killList->getT2($page));
    }

    public function getT3($page = 1) {
        return $this->json($this->killList->getT3($page));
    }

    public function getFrigates($page = 1) {
        return $this->json($this->killList->getFrigates($page));
    }

    public function getDestroyers($page = 1) {
        return $this->json($this->killList->getDestroyers($page));
    }

    public function getCruisers($page = 1) {
        return $this->json($this->killList->getCruisers($page));
    }

    public function getBattleCruisers($page = 1) {
        return $this->json($this->killList->getBattleCruisers($page));
    }

    public function getBattleShips($page = 1) {
        return $this->json($this->killList->getBattleShips($page));
    }

    public function getCapitals($page = 1) {
        return $this->json($this->killList->getCapitals($page));
    }

    public function getFreighters($page = 1) {
        return $this->json($this->killList->getFreighters($page));
    }

    public function getSuperCarriers($page = 1) {
        return $this->json($this->killList->getSuperCarriers($page));
    }

    public function getTitans($page = 1) {
        return $this->json($this->killList->getTitans($page));
    }
}