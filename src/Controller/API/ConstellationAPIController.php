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
use Thessia\Model\Database\EVE\Characters;
use Thessia\Model\Database\EVE\Top;
use Thessia\Model\Database\Site\Search;

/**
 * Class ConstellationAPIController
 * @package Thessia\Controller\API
 */
class ConstellationAPIController extends Controller
{
    /**
     * @var Search
     */
    private $search;
    /**
     * @var \MongoDB\Collection
     */
    private $collection;

    /**
     * ConstellationAPIController constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->collection = $this->mongo->selectCollection("ccp", "constellations");
        $this->search = $this->container->get("search");
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function constellationCount() {
        $count = $this->collection->count();
        return $this->json(array("constellationCount" => $count));
    }

    /**
     * @param int $constellationID
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function constellationInformation(int $constellationID) {
        $info = $this->collection->findOne(array("constellationID" => $constellationID));
        return $this->json($info);
    }

    /**
     * @param string $searchTerm
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function findConstellation(string $searchTerm) {
        return $this->json($this->search->search($searchTerm, array("constellation"), 50)["constellation"]);
    }
}