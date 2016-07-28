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
class Crest {
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
     * @return array
     */
    public function getData(String $path): array {
        $url = "https://crest.eveonline.com{$path}";
        $md5 = md5($url);

        // If it exists in the cache, we'll just get it from there for now
        if($this->cache->exists($md5))
            return $this->cache->get($md5);

        // Consume a token. It will block till one becomes available if none are available.
        $this->consumer->consume(1);

        // Now get the data from CREST, and return it. Also cache it in the db for 5 minutes.
        $data = $this->curl->getData($url, 300);

        // Make sure it's actually json
        if($this->isJson($data)) {
            // Unpack the json to an array
            $data = json_decode($data, true);

            // Store it in the cache for 5 minutes
            $this->cache->set($md5, $data, 300);

            // Return the data
            return $data;
        }

        // If it's not json, return an empty array
        return array();
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