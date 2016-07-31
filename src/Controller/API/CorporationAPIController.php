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
use Thessia\Model\Database\EVE\Corporations;
use Thessia\Model\Database\EVE\Top;
use Thessia\Model\Database\Site\Search;

/**
 * Class CorporationAPIController
 * @package Thessia\Controller\API
 */
class CorporationAPIController extends Controller
{
    /**
     * @var Search
     */
    private $search;
    /**
     * @var Corporations
     */
    private $corporation;
    /**
     * @var Top
     */
    private $top;

    /**
     * CorporationAPIController constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->corporation = $this->container->get("corporations");
        $this->search = $this->container->get("search");
        $this->top = $this->container->get("top");
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function corporationCount() {
        $count = $this->corporation->getCorporationCount();
        return $this->json(array("corporationCount" => $count));
    }

    /**
     * @param int $corporationID
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function corporationInformation(int $corporationID) {
        $information = $this->corporation->getCorporationInformation($corporationID);
        return $this->json($information);
    }

    /**
     * @param string $searchTerm
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function findCorporation(string $searchTerm) {
        return $this->json($this->search->search($searchTerm, array("corporation"), 50)["corporation"]);
    }

    /**
     * @param int $corporationID
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function corporationMembers(int $corporationID) {
        $characters = $this->corporation->getCorporationMembers($corporationID);
        return $this->json($characters);
    }

    /**
     * @param int $corporationID
     * @param int $limit
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function topCharacters(int $corporationID, int $limit = 10) {
        $data = $this->top->topCharacters("corporationID", $corporationID, $limit);
        return $this->json($data);
    }

    /**
     * @param int $corporationID
     * @param int $limit
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function topAlliances(int $corporationID, int $limit = 10) {
        $data = $data = $this->top->topAlliances("corporationID", $corporationID, $limit);
        return $this->json($data);
    }

    /**
     * @param int $corporationID
     * @param int $limit
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function topShips(int $corporationID, int $limit = 10) {
        $data = $data = $this->top->topShips("corporationID", $corporationID, $limit);
        return $this->json($data);
    }

    /**
     * @param int $corporationID
     * @param int $limit
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function topSystems(int $corporationID, int $limit = 10) {
        $data = $data = $this->top->topSystems("corporationID", $corporationID, $limit);
        return $this->json($data);
    }

    /**
     * @param int $corporationID
     * @param int $limit
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function topRegions(int $corporationID, int $limit = 10) {
        $data = $data = $this->top->topRegions("corporationID", $corporationID, $limit);
        return $this->json($data);
    }
}