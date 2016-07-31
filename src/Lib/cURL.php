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

namespace Thessia\Lib;


/**
 * Class cURL
 * @package Thessia\Lib
 */
class cURL
{
    /** @var Cache $cache */
    private $cache;

    /**
     * cURL constructor.
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param string $url
     * @param int $cacheTime
     * @param array $headers
     * @return mixed|null
     */
    public function getData(string $url, int $cacheTime = 3600, array $headers = array())
    {
        $md5 = md5($url);

        $result = $cacheTime > 0 ? $this->cache->get($md5) : null;

        if (!$result) {
            // Merge the headers from the request with the default headers
            $headers = array_merge(array("Connection: keep-alive", "Keep-Alive: timeout=10, max=1000"), $headers);

            // Init curl
            $curl = curl_init();

            // Setup curl
            curl_setopt_array($curl, array(
                CURLOPT_USERAGENT => "DataGetter for Thessia (email: karbowiak@gmail.com / slack (tweetfleet): karbowiak / irc (coldfront): karbowiak)",
                CURLOPT_TIMEOUT => 60,
                CURLOPT_POST => false,
                CURLOPT_FORBID_REUSE => false,
                CURLOPT_ENCODING => "",
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FAILONERROR => true,
            ));

            // Get the data
            $result = curl_exec($curl);

            // Cache the data
            if ($cacheTime > 0) {
                $this->cache->set($md5, $result, $cacheTime);
            }
        }

        return $result;
    }

    /**
     * @param string $url
     * @param array $postData
     * @param array $headers
     * @return mixed
     */
    public function sendData(string $url, $postData = array(), $headers = array())
    {
        // Define default headers
        if (empty($headers))
            $headers = array("Connection: keep-alive", "Keep-Alive: timeout=10, max=1000");

        // Init curl
        $curl = curl_init();

        // Init postLine
        $postLine = "";

        // Populate the $postData
        if (!empty($postData)) {
            foreach ($postData as $key => $value) {
                $postLine .= $key . "=" . $value . "&";
            }
        }

        // Trim the last &
        rtrim($postLine, "&");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "DataPoster for Thessia (email: karbowiak@gmail.com / slack (tweetfleet): karbowiak / irc (coldfront): karbowiak)");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if (!empty($postData)) {
            curl_setopt($curl, CURLOPT_POST, count($postData));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postLine);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }
}