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

namespace Thessia\Model\Database\Site;

use MongoDB\Client;
use MongoDB\Collection;

class Search {
    private $mongodb;

    public function __construct(Client $client) {
        $this->mongodb = $client;
    }

    public function search(string $searchTerm, array $searchIn = array("faction", "alliance", "corporation", "character", "item", "system", "region", "celestial", "constellation"), int $limit = 5): array {
        $valid = array("faction", "alliance", "corporation", "character", "item", "system", "region", "celestial", "constellation");
        $searchArray = array();

        if (!is_array($searchIn))
            $searchIn = array($searchIn);

        foreach ($searchIn as $lookIn) {
            if (in_array($lookIn, $valid)) {
                $singleSearch = $this->$lookIn($searchTerm, 1);
                $multiSearch = $this->$lookIn($searchTerm, $limit);

                if (count($multiSearch) >= 1)
                    $searchArray[$lookIn] = $multiSearch;
                else
                    $searchArray[$lookIn] = $singleSearch;
            }
        }

        return $searchArray;
    }

    private function faction(string $searchTerm, int $limit = 5): array {
        /** @var Collection $collection */
        $collection = $this->mongodb->selectCollection("thessia", "factions");
        return $collection->find(array("\$text" => array("\$search" => "\"$searchTerm\"")), array("score" => array("\$meta" => "textScore"), "sort" => array("\$score" => -1), "projection" => array("_id" => 0), "limit" => $limit))->toArray();
    }

    private function alliance(string $searchTerm, int $limit = 5): array {
        /** @var Collection $collection */
        $collection = $this->mongodb->selectCollection("thessia", "alliances");
        return $collection->find(array("\$text" => array("\$search" => "\"$searchTerm\"")), array("score" => array("\$meta" => "textScore"), "sort" => array("\$score" => -1), "projection" => array("_id" => 0),  "limit" => $limit))->toArray();
    }

    private function corporation(string $searchTerm, int $limit = 5): array {
        /** @var Collection $collection */
        $collection = $this->mongodb->selectCollection("thessia", "corporations");
        return $collection->find(array("\$text" => array("\$search" => "\"$searchTerm\"")), array("score" => array("\$meta" => "textScore"), "sort" => array("\$score" => -1), "projection" => array("_id" => 0),  "limit" => $limit))->toArray();
    }

    private function character(string $searchTerm, int $limit = 5): array {
        /** @var Collection $collection */
        $collection = $this->mongodb->selectCollection("thessia", "characters");
        return $collection->find(array("\$text" => array("\$search" => "\"$searchTerm\"")), array("score" => array("\$meta" => "textScore"), "sort" => array("\$score" => -1), "projection" => array("_id" => 0),  "limit" => $limit))->toArray();
    }

    private function item(string $searchTerm, int $limit = 5): array {
        /** @var Collection $collection */
        $collection = $this->mongodb->selectCollection("ccp", "typeIDs");
        return $collection->find(array("\$text" => array("\$search" => "\"$searchTerm\"")), array("score" => array("\$meta" => "textScore"), "sort" => array("\$score" => -1), "projection" => array("_id" => 0),  "limit" => $limit))->toArray();
    }

    private function system(string $searchTerm, int $limit = 5): array {
        /** @var Collection $collection */
        $collection = $this->mongodb->selectCollection("ccp", "solarSystems");
        return $collection->find(array("\$text" => array("\$search" => "\"$searchTerm\"")), array("score" => array("\$meta" => "textScore"), "sort" => array("\$score" => -1), "projection" => array("_id" => 0),  "limit" => $limit))->toArray();
    }

    private function region(string $searchTerm, int $limit = 5): array {
        /** @var Collection $collection */
        $collection = $this->mongodb->selectCollection("ccp", "regions");
        return $collection->find(array("\$text" => array("\$search" => "\"$searchTerm\"")), array("score" => array("\$meta" => "textScore"), "sort" => array("\$score" => -1), "projection" => array("_id" => 0),  "limit" => $limit))->toArray();
    }

    private function constellation(string $searchTerm, int $limit = 5): array {
        /** @var Collection $collection */
        $collection = $this->mongodb->selectCollection("ccp", "constellations");
        return $collection->find(array("\$text" => array("\$search" => "\"$searchTerm\"")), array("score" => array("\$meta" => "textScore"), "sort" => array("\$score" => -1), "projection" => array("_id" => 0),  "limit" => $limit))->toArray();
    }

    private function celestial(string $searchTerm, int $limit = 5): array {
        /** @var Collection $collection */
        $collection = $this->mongodb->selectCollection("ccp", "celestials");
        return $collection->find(array("\$text" => array("\$search" => "\"$searchTerm\"")), array("score" => array("\$meta" => "textScore"), "sort" => array("\$score" => -1), "projection" => array("_id" => 0),  "limit" => $limit))->toArray();
    }

}