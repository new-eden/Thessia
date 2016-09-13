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

$app->group("", function() use ($app) {
    $controller = new \Thessia\Controller\KillsController($app);
    $app->get("/latest/[{page:[0-9]+}/]", $controller("latest"));
    $app->get("/bigkills/[{page:[0-9]+}/]", $controller("bigKills"));
    $app->get("/wspace/[{page:[0-9]+}/]", $controller("wormholeSpace"));
    $app->get("/highsec/[{page:[0-9]+}/]", $controller("highSec"));
    $app->get("/lowsec/[{page:[0-9]+}/]", $controller("lowSec"));
    $app->get("/nullsec/[{page:[0-9]+}/]", $controller("nullSec"));
    $app->get("/solo/[{page:[0-9]+}/]", $controller("soloKills"));
    $app->get("/npc/[{page:[0-9]+}/]", $controller("npcKills"));
    $app->get("/5b/[{page:[0-9]+}/]", $controller("fiveBKills"));
    $app->get("/10b/[{page:[0-9]+}/]", $controller("tenBKills"));
    $app->get("/citadels/[{page:[0-9]+}/]", $controller("citadels"));
    $app->get("/t1/[{page:[0-9]+}/]", $controller("t1"));
    $app->get("/t2/[{page:[0-9]+}/]", $controller("t2"));
    $app->get("/t3/[{page:[0-9]+}/]", $controller("t3"));
    $app->get("/frigates/[{page:[0-9]+}/]", $controller("frigates"));
    $app->get("/destroyers/[{page:[0-9]+}/]", $controller("destroyers"));
    $app->get("/cruisers/[{page:[0-9]+}/]", $controller("cruisers"));
    $app->get("/battlecruisers/[{page:[0-9]+}/]", $controller("battleCruisers"));
    $app->get("/battleships/[{page:[0-9]+}/]", $controller("battleShips"));
    $app->get("/capitals/[{page:[0-9]+}/]", $controller("capitals"));
    $app->get("/freighters/[{page:[0-9]+}/]", $controller("freighters"));
    $app->get("/supercarriers/[{page:[0-9]+}/]", $controller("superCarriers"));
    $app->get("/titans/[{page:[0-9]+}/]", $controller("titans"));
});

