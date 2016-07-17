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

class ItemAPIController extends Controller
{
    /**
     * @var \MongoDB\Collection
     */
    private $collection;
    /**
     * @var \MongoDB\Collection
     */
    private $killmails;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->collection = $this->mongo->selectCollection("ccp", "typeIDs");
        $this->killmails = $this->mongo->selectCollection("thessia", "killmails");
    }

    public function itemCount() {
        $count = $this->collection->count();

        return $this->json(array("itemCount" => $count));
    }

    public function itemInformation(int $itemID) {
        $info = $this->collection->find(array("typeID" => $itemID))->toArray();

        return $this->json($info);
    }

    public function findItem(string $searchTerm) {
        $find = $this->collection->find(array("\$text" => array("\$search" => "\"$searchTerm\"")), array("score" => array("\$meta" => "textScore"), "sort" => array("\$score" => -1), "limit" => 50))->toArray();
        return $this->json($find);
    }
}