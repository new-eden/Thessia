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

namespace Thessia\Tasks\Resque;


use League\Container\Container;
use Thessia\Lib\cURL;
use Thessia\Model\EVE\Crest;

class CrestKillmailFetcher
{
    /**
     * @var Container
     */
    private $container;

    public function perform()
    {
        /** @var cURL $curl */
        $curl = $this->container->get("curl");
        /** @var Crest $crest */
        $crest = $this->container->get("crest");

        $url = $this->args["url"];
        $warID = $this->args["warID"] ? $this->args["warID"] : 0;

        $data = json_decode($curl->getData($url, 0), true);

        if (isset($data["killID"])) {
            $source = isset($warID) ? "warID:{$warID}" : "CREST:{$data["killID"]}";
            $killID = $data["killID"];
            $hash = $crest->generateHash($data);

            \Resque::enqueue("rt", '\Thessia\Tasks\Resque\KillmailParser',
                array("killID" => $killID, "killHash" => $hash));
        }

        exit();
    }

    public function setUp()
    {
        $this->container = getContainer();
    }

    public function tearDown()
    {

    }
}