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
 * Class CharacterAPIController
 * @package Thessia\Controller\API
 */
class CharacterAPIController extends Controller
{
    /**
     * @var Search
     */
    private $search;
    /**
     * @var Characters
     */
    private $character;
    /**
     * @var Top
     */
    private $top;

    /**
     * CharacterAPIController constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->character = $this->container->get("characters");
        $this->search = $this->container->get("search");
        $this->top = $this->container->get("top");
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function characterCount() {
        $count = $this->character->getCharacterCount();
        return $this->json(array("characterCount" => $count));
    }

    /**
     * @param int $characterID
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function characterInformation(int $characterID) {
        $info = $this->character->getCharacterInformation($characterID);
        return $this->json($info);
    }

    /**
     * @param string $searchTerm
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function findCharacter(string $searchTerm) {
        return $this->json($this->search->search($searchTerm, array("character"), 50)["character"]);
    }

    /**
     * @param int $characterID
     * @param int $limit
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function topCorporations(int $characterID, int $limit = 10) {
        $data = $this->top->topCorporations("characterID", $characterID, $limit);
        return $this->json($data);
    }

    /**
     * @param int $characterID
     * @param int $limit
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function topAlliances(int $characterID, int $limit = 10) {
        $data = $data = $this->top->topAlliances("characterID", $characterID, $limit);
        return $this->json($data);
    }

    /**
     * @param int $characterID
     * @param int $limit
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function topShips(int $characterID, int $limit = 10) {
        $data = $data = $this->top->topShips("characterID", $characterID, $limit);
        return $this->json($data);
    }

    /**
     * @param int $characterID
     * @param int $limit
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function topSystems(int $characterID, int $limit = 10) {
        $data = $data = $this->top->topSystems("characterID", $characterID, $limit);
        return $this->json($data);
    }

    /**
     * @param int $characterID
     * @param int $limit
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function topRegions(int $characterID, int $limit = 10) {
        $data = $data = $this->top->topRegions("characterID", $characterID, $limit);
        return $this->json($data);
    }
}