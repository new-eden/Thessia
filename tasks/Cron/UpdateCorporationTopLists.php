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

class UpdateCorporationTopLists
{
    /**
     * @param Container $container
     */
    public static function execute(Container $container)
    {
        $log = $container->get("log");
        $startTime = time();

        //$log->addInfo("Updating Top10 Characters");
        //self::updateTop10Characters(false, $container);

        /*
         * Run every 6 hours if possible, and cache the data for 12 hours
         * Should leave enough room to go through all corporations and alliances...........................
         */
        exit;
    }

    /**
     * Defines how often the cronjob runs, every 1 second, every 60 seconds, every 86400 seconds, etc.
     */
    public static function getRunTimes()
    {
        return 3600;
    }
    
    /**
     * @param $dateTime
     * @return UTCDatetime
     */
    private static function makeTimeFromDateTime($dateTime): UTCDatetime {
        $unixTime = strtotime($dateTime);
        $milliseconds = $unixTime * 1000;

        return new UTCDatetime($milliseconds);
    }

    /**
     * @param $unixTime
     * @return UTCDatetime
     */
    private static function makeTimeFromUnixTime($unixTime): UTCDatetime {
        $milliseconds = $unixTime * 1000;
        return new UTCDatetime($milliseconds);
    }
}