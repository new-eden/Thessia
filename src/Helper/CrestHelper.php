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

namespace Thessia\Helper;


use bandwidthThrottle\tokenBucket\BlockingConsumer;
use bandwidthThrottle\tokenBucket\Rate;
use bandwidthThrottle\tokenBucket\storage\FileStorage;
use bandwidthThrottle\tokenBucket\TokenBucket;
use Thessia\Lib\Cache;
use Thessia\Lib\cURL;

/**
 * Class Crest
 * @package Thessia\Helper
 */
class CrestHelper {
    /**
     * @var BlockingConsumer
     */
    private $consumer;
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var cURL
     */
    private $curl;

    /**
     * Crest constructor.
     * @param Cache $cache
     * @param cURL $curl
     */
    public function __construct(Cache $cache, cURL $curl) {
        $this->cache = $cache;
        $this->curl = $curl;

        $fs = new FileStorage(__DIR__ . "/../../cache/crest.bucket");
        $rate = new Rate(150, Rate::SECOND);
        $bucket = new TokenBucket(150, $rate, $fs);
        $this->consumer = new BlockingConsumer($bucket);
        $bucket->bootstrap(150);
    }

    /**
     * @param String $path
     * @param int $cacheTTL
     * @return array
     */
    private function getData(String $path, int $cacheTTL = 3600): array {
        $url = "https://crest.eveonline.com{$path}";
        $md5 = md5($url);

        // If it exists in the cache, we'll just get it from there for now
        if($this->cache->exists($md5))
            return json_decode($this->cache->get($md5), true);

        // Consume a token. It will block till one becomes available if none are available.
        $this->consumer->consume(1);

        // Now get the data from CREST, and return it. Also cache it in the db for 5 minutes.
        $data = $this->curl->getData($url, $cacheTTL);

        // Make sure it's actually json
        if($this->isJson($data)) {
            // Unpack the json to an array
            $data = json_decode($data, true);

            // Store it in the cache for 5 minutes
            $this->cache->set($md5, json_encode($data), $cacheTTL);

            // Return the data
            return $data;
        }

        // If it's not json, return an empty array
        return array();
    }

    /**
     * Return all the wars available.
     *
     * @cacheTime 1 day
     * @param int $page
     * @return array
     */
    public function getWars(int $page = 1): array {
        $url = "/wars/?page={$page}";
        return $this->getData($url, 86400);
    }

    /**
     * Return a single wars information
     *
     * @cacheTime 1 hour
     * @param int $warID
     * @return array
     */
    public function getWar(int $warID): array {
        $url = "/wars/{$warID}/";
        return $this->getData($url, 3600);
    }

    /**
     * Return all the killmails for a war
     *
     * @cacheTime 1 hour
     * @param int $warID
     * @param int $page
     * @return array
     */
    public function getWarKillmails(int $warID, int $page = 1): array {
        $url = "/wars/{$warID}/killmails/all/?page={$page}";
        return $this->getData($url, 3600);
    }

    /**
     * Return all the Types available in EVE
     *
     * @cacheTime 1 hour
     * @param int $page
     * @return array
     */
    public function getTypes(int $page = 1) {
        $url = "/inventory/types/?page={$page}";
        return $this->getData($url, 3600);
    }

    /**
     * Return data for a single Type
     *
     * @cacheTime 1 hour
     * @param int $typeID
     * @return array
     */
    public function getType(int $typeID) {
        $url = "/inventory/types/{$typeID}/";
        return $this->getData($url, 3600);
    }

    /**
     * Get the current EVE time
     *
     * @cacheTime 10 seconds
     * @return array
     */
    public function getEveTime() {
        $url = "/time/";
        return $this->getData($url, 10);
    }

    /**
     * Get the insurance values for ships in EVE
     *
     * @cacheTime 1 hour
     * @return array
     */
    public function getInsurancePrices() {
        $url = "/insuranceprices/";
        return $this->getData($url, 3600);
    }

    /**
     * Get a list of all alliances in EVE
     *
     * @cacheTime 30 minutes
     * @param int $page
     * @return array
     */
    public function getAlliances(int $page = 1) {
        $url = "/alliances/?page={$page}";
        return $this->getData($url, 1800);
    }

    /**
     * Get information for a single alliance in EVE
     *
     * @cacheTime 30 minutes
     * @param int $allianceID
     * @return array
     */
    public function getAlliance(int $allianceID) {
        $url = "/alliances/{$allianceID}/";
        return $this->getData($url, 1800);
    }

    /**
     * Get Market Pricing information.
     *
     * @cacheTime: 23 hours
     * @param int $page
     * @return array
     */
    public function getMarketPrices(int $page = 1) {
        $url = "/market/prices/?page{$page}";
        return $this->getData($url, 82800);
    }

    public function updateToken() {

    }

    /**
     * Returns true if json, false if not.
     *
     * @param string $json
     * @return bool
     */
    private function isJson(string $json): bool {
        json_decode($json);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}