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

use MongoDB\BSON\UTCDatetime;
use MongoDB\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pheal\Cache\RedisStorage;
use Pheal\Core\Config;
use Pheal\Fetcher\Curl;
use Pheal\Fetcher\Guzzle;
use Pheal\Log\PsrLogger;
use Pheal\RateLimiter\FileLockRateLimiter;
use Thessia\Model\Database\Site\Storage;

/**
 * Class Pheal
 * @package Thessia\Helper
 */
class Pheal
{
    /**
     * @var null|Config
     */
    private $pheal;
    /**
     * @var Storage
     */
    private $storage;
    /**
     * @var Client
     */
    private $mongo;

    /**
     * Pheal constructor.
     * @param Storage $storage
     * @param Client $mongo
     */
    function __construct(Storage $storage, Client $mongo) {
        $this->pheal = Config::getInstance();
        $this->storage = $storage;
        $this->mongo = $mongo;

        $this->pheal->fetcher = new Curl();
        $this->pheal->http_user_agent = "DataGetter for Thessia (email: karbowiak@gmail.com / slack (tweetfleet): karbowiak / irc (coldfront): karbowiak)";
        $this->pheal->http_post = false;
        $this->pheal->http_keepalive = 10;
        $this->pheal->http_timeout = 30;
        $this->pheal->cache = new RedisStorage(array(
            "host" => "127.0.0.1",
            "port" => 6379,
            "persistent" => true,
            "auth" => null,
            "prefix" => "pheal_"
        ));

        $this->pheal->log = new PsrLogger($this->log());
        $this->pheal->api_customkeys = true;
        $this->pheal->api_base = "https://api.eveonline.com/";
        $this->pheal->rateLimiter = new FileLockRateLimiter(__DIR__ . "/../../cache/", 60, 30, 10);
    }

    /**
     * @return Logger
     */
    private function log(): Logger {
        $psrLogger = new Logger("Pheal");
        $psrLogger->pushHandler(new StreamHandler(__DIR__ . "/../../logs/pheal.log", Logger::INFO));

        return $psrLogger;
    }

    /**
     * @param int|null $apiKey
     * @param string|null $vCode
     * @return \Pheal\Pheal
     * @throws \Exception
     */
    public function Pheal(int $apiKey = null, string $vCode = null): \Pheal\Pheal {
        // Verify that we're not in a 904, by poking the storage
        $nineOhFour = $this->storage->get("ccp904");

        if(isset($nineOhFour) && $nineOhFour >= date("Y-m-d H:i:s")) {
            $this->log()->addWarning("904'ed until {$nineOhFour}");
            throw new \Exception("Error, 904'ed until {$nineOhFour}");
        }

        return new \Pheal\Pheal($apiKey, $vCode);
    }

    public function is904ed() {
        $nineOhFour = $this->storage->get("ccp904");
        if(isset($nineOhFour) && $nineOhFour >= date("Y-m-d H:i:s"))
            return true;
        return false;
    }

    /**
     * @param int $apiKey
     * @param int $characterID
     * @param \Exception $exception
     */
    public function handleApiException(int $apiKey = null, int $characterID = null, \Exception $exception) {
        $code = $exception->getCode();
        $message = $exception->getMessage();
        $collection = $this->mongo->selectCollection("thessia", "apiKeys");

        switch($code) {
            case 904: // Temp ban from CCPs API server
            case 28: // Timeouts
                $this->storage->set("ccp904", date("Y-m-d H:i:s", time() + 300));
                break;

            case 403:
            case 502:
            case 503: // Service Unavailable - try again later
                if($characterID)
                    $collection->updateOne(array("keyID" => $apiKey), array("\$set" => array("characters.{$characterID}.cachedUntil" => new UTCDatetime((time() + 300) * 1000))));
                break;

            case 119: // Kills exhausted: retry after [{0}]
                if($characterID && isset($exception->cached_until))
                    $collection->updateOne(array("keyID" => $apiKey), array("\$set" => array("characters.{$characterID}.cachedUntil" => new UTCDatetime(strtotime($exception->cached_until) * 1000))));
                break;

            case 120: // Expected beforeKillID [{0}] but supplied [{1}]: kills previously loaded.
                if($characterID && isset($exception->cached_until))
                    $collection->updateOne(array("keyID" => $apiKey), array("\$set" => array("characters.{$characterID}.cachedUntil" => new UTCDatetime(strtotime($exception->cached_until) * 1000))));
                break;

            case 221: // Demote toon, illegal page access
                $collection->deleteOne(array("keyID" => $apiKey));
                $this->log()->info("Deleted apiKey: {$apiKey}. reason: {$message}");
                break;

            case 220: // Current security level not high enough.
            case 200: // Typically happens when a key isn't a full API Key
                $collection->deleteOne(array("keyID" => $apiKey));
                $this->log()->info("Deleted apiKey: {$apiKey}. reason: {$message}");
                break;

            case 522: // Character does not belong to account.
            case 201: // Typically caused by a character transfer
                $collection->deleteOne(array("keyID" => $apiKey));
                $this->log()->info("Deleted apiKey: {$apiKey}. reason: {$message}");
                break;

            case 207: // Not available for NPC corporations.
            case 209:
                $collection->updateOne(array("keyID" => $apiKey), array("\$set" => array("characters" => array())));
                break;

            case 222: // account has expired
                $collection->deleteOne(array("keyID" => $apiKey));
                $this->log()->info("Deleted apiKey: {$apiKey}. reason: {$message}");
                break;

            // Login denied by account status
            // Remove characters, will revalidate with next key update
            case 211:
                $collection->updateOne(array("keyID" => $apiKey), array("\$set" => array("characters" => array())));
                $collection->updateOne(array("keyID" => $apiKey), array("\$set" => array("errorCode" => $code)));
                break;

            case 202: // API key authentication failure.
            case 203: // Authentication failure - API is no good and will never be good again
            case 204: // Authentication failure.
            case 205: // Authentication failure (final pass).
            case 210: // Authentication failure.
            case 521: // Invalid username and/or password passed to UserData.LoginWebUser().
                $collection->updateOne(array("keyID" => $apiKey), array("\$set" => array("characters" => array())));
                $collection->updateOne(array("keyID" => $apiKey), array("\$set" => array("errorCode" => $code)));
                break;

            case 500: // Internal Server Error (More CCP Issues)
            case 520: // Unexpected failure accessing database. (More CCP issues)
            case 404: // URL Not Found (CCP having issues...)
            case 902: // Eve backend database temporarily disabled
                if($characterID)
                    $collection->updateOne(array("keyID" => $apiKey), array("\$set" => array("characters.{$characterID}.cachedUntil" => new UTCDatetime((time() + 3600) * 1000))));
                break;

            case 0: // API Date could not be read / parsed, original exception (Something is wrong with the XML and it couldn't be parsed)
            default: // try again in 5 minutes
                $this->log()->addWarning("{$apiKey} - Unhandled error - Code: {$code} / Message: {$message}");
                $collection->updateOne(array("keyID" => $apiKey), array("\$set" => array("errorCode" => $code)));
                break;
        }

        // Sleep for one second if we make an error
        sleep(1);
    }

}