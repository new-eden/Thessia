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

/**
 * Created by PhpStorm.
 * User: micha
 * Date: 22-07-2016
 * Time: 01:06
 */

namespace Thessia\Helper;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pheal\Cache\RedisStorage;
use Pheal\Core\Config;
use Pheal\Fetcher\Guzzle;
use Pheal\Log\PsrLogger;
use Pheal\RateLimiter\FileLockRateLimiter;
use Thessia\Model\Site\Storage;

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
     * Pheal constructor.
     * @param Storage $storage
     */
    function __construct(Storage $storage) {
        $this->pheal = Config::getInstance();
        $this->storage = $storage;

        $this->pheal->fetcher = new Guzzle();
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
        $this->pheal->rateLimiter = new FileLockRateLimiter(__DIR__ . "/../../cache/", 30, 10, 10);
    }

    /**
     * @return Logger
     */
    public function log() {
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
    public function Pheal(int $apiKey = null, string $vCode = null) {
        // Verify that we're not in a 904, by poking the storage
        $nineOhFour = $this->storage->get("ccp904");

        if(isset($nineOhFour) && $nineOhFour >= date("Y-m-d H:i:s")) {
            $this->log()->addWarning("904'ed until {$nineOhFour}");
            throw new \Exception("Error, 904'ed until {$nineOhFour}");
        }

        return new \Pheal\Pheal($apiKey, $vCode);
    }

    /**
     * @param int $apiKey
     * @param int $characterID
     * @param \Exception $exception
     */
    public function handleApiException(int $apiKey, int $characterID, \Exception $exception) {
        $code = $exception->getCode();
        $message = $exception->getMessage();

        switch($code) {
            case 904: // temp ban from CCPs api server
            case 28: // Timeouts
                break;

            case 403:
            case 502:
            case 503: // Service Unavailable - try again later
                break;
            case 119: // Kills exhausted: retry after [{0}]
                break;
            case 120: // Expected beforeKillID [{0}] but supplied [{1}]: kills previously loaded.
                break;
            case 221: // Demote toon, illegal page access
                break;
            case 220:
            case 200: // Current security level not high enough.
                // Typically happens when a key isn't a full API Key
                break;
            case 522:
            case 201: // Character does not belong to account.
                // Typically caused by a character transfer
                break;
            case 207: // Not available for NPC corporations.
            case 209:
                break;
            case 222: // account has expired
                break;
            case 403:
            case 211: // Login denied by account status
                // Remove characters, will revalidate with next doPopulate
                break;
            case 202: // API key authentication failure.
            case 203: // Authentication failure - API is no good and will never be good again
            case 204: // Authentication failure.
            case 205: // Authentication failure (final pass).
            case 210: // Authentication failure.
            case 521: // Invalid username and/or password passed to UserData.LoginWebUser().
                break;
            case 500: // Internal Server Error (More CCP Issues)
            case 520: // Unexpected failure accessing database. (More CCP issues)
            case 404: // URL Not Found (CCP having issues...)
            case 902: // Eve backend database temporarily disabled
                break;
            case 0: // API Date could not be read / parsed, original exception (Something is wrong with the XML and it couldn't be parsed)
            default: // try again in 5 minutes
                $this->log()->addWarning("{$apiKey} - Unhandled error - Code: {$code} / Message: {$message}");
                break;
        }
    }

}