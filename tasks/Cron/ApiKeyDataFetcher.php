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

namespace Thessia\Tasks\Cron;

use League\Container\Container;
use MongoDB\BSON\UTCDatetime;
use MongoDB\Collection;
use Monolog\Logger;
use Thessia\Helper\CrestHelper;
use Thessia\Helper\EVEApi\Account;
use Thessia\Helper\EVEApi\Character;
use Thessia\Helper\EVEApi\Corporation;
use Thessia\Helper\Pheal;
use Thessia\Model\Database\EVE\Killmails;

/**
 * Class ApiKeyDataFetcher
 * @package Thessia\Tasks\Cron
 */
class ApiKeyDataFetcher {
    private $container;
    public function perform()
    {
        /** @var \MongoClient $mongo */
        $mongo = $this->container->get("mongo");
        /** @var Logger $log */
        $log = $this->container->get("log");
        /** @var Pheal $pheal */
        $pheal = $this->container->get("pheal");
        /** @var Collection $collection */
        $collection = $mongo->selectCollection("thessia", "apiKeys");

        if($pheal->is904ed()) {
            $log->addInfo("CRON: 904ed..");
            exit;
        }

        $log->addInfo("CRON: Fetching data from the API");
        // Get all keys that had their last validation 1 hour ago
        $date = strtotime(date("Y-m-d H:i:s", strtotime("-1 hour"))) * 1000;
        $keys = $collection->aggregate(array(
            array("\$unwind" => "\$characters"),
            array("\$match" => array("expires" => array("\$gte" => new UTCDatetime(time() * 1000)), "characters.cachedUntil" => array("\$lt" => new UTCDatetime($date))))
        ))->toArray();

        foreach($keys as $key) {
            $apiKey = (int) $key["keyID"];
            $vCode = $key["vCode"];
            $accessMask = (int) $key["accessMask"];
            $characterID = (int) $key["characters"]["characterID"];
            $accountType = $key["keyType"];
            $cachedUntil = date("Y-m-d H:i:s", strtotime("+1 hour")) * 1000;

            // Killmails
            if(($accessMask & 256) > 0)
                $cachedUntil = $this->killmails($apiKey, $vCode, $characterID, $accountType);

            // Update the cached until to last for an hour from now
            $collection->updateOne(array("keyID" => $apiKey, "characters.characterID" => $characterID), array("\$set" => array("characters.\$.cachedUntil" => new UTCDatetime($cachedUntil))));
        }

        exit;
    }

    /**
     * @param int $apiKey
     * @param string $vCode
     * @param int|null $characterID
     * @param string $accountType
     * @return int
     */
    private function killmails(int $apiKey, string $vCode, int $characterID = null, string $accountType): int {
        /** @var Logger $log */
        $log = $this->container->get("log");
        /** @var Corporation $corporation */
        $corporation = $this->container->get("ccpCorporation");
        /** @var Character $character */
        $character = $this->container->get("ccpCharacter");
        /** @var CrestHelper $crestHelper */
        $crestHelper = $this->container->get("crestHelper");
        /** @var Killmails $kms */
        $kms = $this->container->get("killmails");

        // Default cache time
        $cachedUntil = date("Y-m-d H:i:s", strtotime("+1 hour")) * 1000;

        // Fetch the killmail data, and pass it onto the killmail fetcher...
        if ($accountType == "Account") // It's a corporation key
            $data = $corporation->corporationKillMails($apiKey, $vCode);
        elseif ($accountType == "Character") // It's a character key
            $data = $character->characterKillMails($apiKey, $vCode, $characterID);

        if(isset($data["cachedUntil"])) {
            $cachedUntil = strtotime($data["cachedUntil"]) * 1000;
            $killmails = $data["result"]["kills"];

            if (count($killmails) > 0) {
                foreach($killmails as $mail) {
                    $killID = (int) $mail["killID"];

                    // Check if the killmail isn't already in the database
                    $exists = $kms->getAllByKillID($killID)->toArray();
                    if(!empty($exists))
                        continue;

                    // Generate the crest hash
                    $crestHash = $crestHelper->generateCRESTHash($mail);

                    // If crestHash was generated properly, we'll post the mail!
                    if(is_string($crestHash)) {
                        $log->addInfo("Adding {$killID} from apiKey {$apiKey} to the database...");
                        \Resque::enqueue("rt", '\Thessia\Tasks\Resque\KillmailParser', array("killID" => $killID, "killHash" => $crestHash));
                    }
                }
            }
        }

        // Return the cached until time
        return $cachedUntil;
    }

    /**
     * Defines how often the cronjob runs, every 1 second, every 60 seconds, every 86400 seconds, etc.
     */
    public function getRunTimes()
    {
        return 60;
    }

    public function setUp()
    {
        $this->container = getContainer();
    }

    public function tearDown()
    {

    }
}