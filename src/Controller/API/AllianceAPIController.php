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
use Thessia\Model\Database\EVE\Alliances;
use Thessia\Model\Database\EVE\Top;
use Thessia\Model\Database\Site\Search;

/**
 * Class AllianceAPIController
 * @package Thessia\Controller\API
 */
class AllianceAPIController extends Controller
{
    /**
     * @var Search
     */
    private $search;
    /**
     * @var Alliances
     */
    private $alliance;
    /**
     * @var Top
     */
    private $top;

    /**
     * AllianceAPIController constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->alliance = $this->container->get("alliances");
        $this->search = $this->container->get("search");
        $this->top = $this->container->get("top");
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function allianceCount() {
        $count = $this->alliance->getAllianceCount();
        return $this->json(array("allianceCount" => $count));
    }

    /**
     * @param int $corporationID
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function allianceInformation(int $corporationID) {
        $information = $this->alliance->getAllianceInformation($corporationID);
        return $this->json($information);
    }

    /**
     * @param string $searchTerm
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function findAlliance(string $searchTerm) {
        return $this->json($this->search->search($searchTerm, array("alliance"), 50)["alliance"]);
    }

    /**
     * @param int $corporationID
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function allianceMembers(int $corporationID) {
        $characters = $this->alliance->getAllianceMembers($corporationID);
        return $this->json($characters);
    }

    /**
     * @param int $allianceID
     * @param int $limit
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function topCharacters(int $allianceID, int $limit = 10) {
        $data = $this->top->topCharacters("allianceID", $allianceID, $limit);
        return $this->json($data);
    }

    /**
     * @param int $allianceID
     * @param int $limit
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function topCorporations(int $allianceID, int $limit = 10) {
        $data = $this->top->topCorporations("allianceID", $allianceID, $limit);
        return $this->json($data);
    }

    /**
     * @param int $allianceID
     * @param int $limit
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function topShips(int $allianceID, int $limit = 10) {
        $data = $data = $this->top->topShips("allianceID", $allianceID, $limit);
        return $this->json($data);
    }

    /**
     * @param int $allianceID
     * @param int $limit
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function topSystems(int $allianceID, int $limit = 10) {
        $data = $data = $this->top->topSystems("allianceID", $allianceID, $limit);
        return $this->json($data);
    }

    /**
     * @param int $allianceID
     * @param int $limit
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function topRegions(int $allianceID, int $limit = 10) {
        $data = $data = $this->top->topRegions("allianceID", $allianceID, $limit);
        return $this->json($data);
    }
}