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
use Thessia\Helper\EVEApi\Account;
use Thessia\Helper\Pheal;

class ApiKeyChecker {
    /**
     * @param Container $container
     */
    public static function execute(Container $container)
    {
        /** @var \MongoClient $mongo */
        $mongo = $container->get("mongo");
        /** @var Logger $log */
        $log = $container->get("log");
        /** @var Pheal $pheal */
        $pheal = $container->get("pheal");
        /** @var Account $account */
        $account = $container->get("ccpAccount");
        /** @var Collection $collection */
        $collection = $mongo->selectCollection("thessia", "apiKeys");

        if($pheal->is904ed()) {
            $log->addInfo("CRON: 904ed..");
            exit;
        }

        $log->addInfo("CRON: Updating API keys");
        $date = strtotime(date("Y-m-d H:i:s", strtotime("-6 hour"))) * 1000;
        $apiKeys = $collection->find(array("lastValidation" => array("\$lt" => new UTCDatetime($date))), array("limit" => 100))->toArray();

        foreach ($apiKeys as $api) {
            $keyID = $api["keyID"];
            $vCode = $api["vCode"];

            $keyInfo = $account->accountAPIKeyInfo($keyID, $vCode);

            if(isset($keyInfo["result"])) {
                foreach ($keyInfo["result"] as $key) {
                    $keyType = $key["type"];
                    $accessMask = $key["accessMask"];
                    $expires = !empty($key["expires"]) ? strtotime($key["expires"]) * 1000 : strtotime("2038-01-01 00:00:00") * 1000;

                    $keyData = array(
                        "keyID" => $keyID,
                        "vCode" => $vCode,
                        "errorCode" => 0,
                        "lastValidation" => new UTCDatetime(time() * 1000),
                        "keyType" => $keyType,
                        "accessMask" => $accessMask,
                        "expires" => new UTCDatetime($expires),
                    );

                    foreach ($key["characters"] as $char) {
                        $keyData["characters"][] = array(
                            "characterID" => (int) $char["characterID"],
                            "characterName" => $char["characterName"],
                            "corporationID" => (int) $char["corporationID"],
                            "corporationName" => $char["corporationName"],
                            "allianceID" => (int) $char["allianceID"],
                            "allianceName" => $char["allianceName"],
                            "factionID" => (int) $char["factionID"],
                            "factionName" => $char["factionName"],
                            "isDirector" => (bool) $keyType == "Corporation" ? true : false,
                            "cachedUntil" => new UTCDatetime(null),
                        );
                    }

                    $collection->replaceOne(array("keyID" => $keyID), $keyData, array("upsert" => true));
                }
            }
        }
    }

    /**
     * Defines how often the cronjob runs, every 1 second, every 60 seconds, every 86400 seconds, etc.
     */
    public static function getRunTimes()
    {
        return 60;
    }
}