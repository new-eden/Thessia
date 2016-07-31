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
use Thessia\Model\Database\Site\Search;

/**
 * Class SearchAPIController
 * @package Thessia\Controller\API
 */
class SearchAPIController extends Controller {
    /**
     * @var Search
     */
    private $search;

    /**
     * SearchAPIController constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->search = $this->container->get("search");
    }

    /**
     * @param string $searchTerm
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function findCharacter(string $searchTerm) {
        return $this->json($this->search->search($searchTerm, array("character"))["character"]);
    }

    /**
     * @param string $searchTerm
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function findCorporation(string $searchTerm) {
        return $this->json($this->search->search($searchTerm, array("corporation"))["corporation"]);
    }

    /**
     * @param string $searchTerm
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function findAlliance(string $searchTerm) {
        return $this->json($this->search->search($searchTerm, array("alliance"))["alliance"]);
    }

    /**
     * @param string $searchTerm
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function findFaction(string $searchTerm) {
        return $this->json($this->search->search($searchTerm, array("faction"))["faction"]);
    }

    /**
     * @param string $searchTerm
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function findSolarSystem(string $searchTerm) {
        return $this->json($this->search->search($searchTerm, array("system"))["system"]);
    }

    /**
     * @param string $searchTerm
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function findRegion(string $searchTerm) {
        return $this->json($this->search->search($searchTerm, array("region"))["region"]);
    }

    /**
     * @param string $searchTerm
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function findCelestial(string $searchTerm) {
        return $this->json($this->search->search($searchTerm, array("celestial"))["celestial"]);
    }

    /**
     * @param string $searchTerm
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function findItem(string $searchTerm) {
        return $this->json($this->search->search($searchTerm, array("item"))["item"]);
    }
}