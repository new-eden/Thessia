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
use MongoDB\Client;
use MongoDB\Collection;
use Ratchet\WebSocket\Version\RFC6455\Connection;
use Thessia\Lib\Config;
use Thessia\Model\EVE\Parser;

class KillmailParser
{
    /**
     * @var Container
     */
    private $container;

    public function perform()
    {
        /** @var Client $mongodb */
        $mongodb = $this->container->get("mongo");
        /** @var Collection $collection */
        $collection = $mongodb->selectCollection("thessia", "killmails");
        /** @var Parser $parser */
        $parser = $this->container->get("parser");
        /** @var Config $config */
        $config = $this->container->get("config");

        $killID = (int) $this->args["killID"];
        $killHash = (string) $this->args["killHash"];
        $warID = isset($this->args["warID"]) ? (int) $this->args["warID"] : 0;

        // It happens that there are a \n in the hash, remove it so the hash functions
        if(stristr($killHash, "\n"))
            $killHash = str_replace("\n", "", $killHash);

        // @todo add a way to force update a mail
        // Make sure the mail doesn't already exist before we bother CREST
        $exists = $collection->findOne(array("killID" => $killID));
        if(!empty($exists)) {
            echo "Killmail already exists...\n";
            exit;
        }

        // Throw the killID, hash and warID (if there is one) at the parser, and let it generate a nice pretty killmail for us.
        $killmail = $parser->parseCrestKillmail($killID, $killHash, $warID);

        // If the killmail is an array, we try and insert it - if it already exists, we'll just update it.
        if (is_array($killmail)) {
            try {
                $collection->insertOne($killmail);
            } catch(\Exception $e) {
                $collection->replaceOne(array("killID" => $killID), $killmail, array("upsert" => true));
            }

            // Send it to the websocket as well, if it fails, do nothing
            try {
                \Ratchet\Client\connect($config->get("serverAddress", "websocket"))->then(function (Connection $conn
                ) use ($killmail) {
                    $conn->send(json_encode($killmail, JSON_NUMERIC_CHECK));
                    $conn->close();
                });
            } catch(\Exception $e) {}
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