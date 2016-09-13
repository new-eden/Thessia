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

namespace Thessia\Controller;


use Thessia\Middleware\Controller;

class KillsController extends Controller {
    public function latest($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "Latest", "type" => "latest", "page" => $page));
    }

    public function bigKills($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "Big Kills", "type" => "bigkills", "page" => $page));
    }

    public function wormholeSpace($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "Wormhole Space", "type" => "wspace", "page" => $page));
    }

    public function highSec($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "High Sec", "type" => "highsec", "page" => $page));
    }

    public function lowSec($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "Low Sec", "type" => "lowsec", "page" => $page));
    }

    public function nullSec($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "Null Sec", "type" => "nullsec", "page" => $page));
    }

    public function soloKills($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "Solo Kills", "type" => "solo", "page" => $page));
    }

    public function npcKills($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "NPC Kills", "type" => "npc", "page" => $page));
    }

    public function fiveBKills($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "+5b Kills", "type" => "5b", "page" => $page));
    }

    public function tenBKills($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "+10b Kills", "type" => "10b", "page" => $page));
    }

    public function citadels($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "Citadels", "type" => "citadels", "page" => $page));
    }

    public function t1($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "T1 Ships", "type" => "t1", "page" => $page));
    }

    public function t2($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "T2 Ships", "type" => "t2", "page" => $page));
    }

    public function t3($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "T3 Ships", "type" => "t3", "page" => $page));
    }

    public function frigates($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "Frigates", "type" => "frigates", "page" => $page));
    }

    public function destroyers($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "Destroyers", "type" => "destroyers", "page" => $page));
    }

    public function cruisers($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "Cruisers", "type" => "cruisers", "page" => $page));
    }

    public function battleCruisers($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "BattleCruisers", "type" => "battlecruisers", "page" => $page));
    }

    public function battleships($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "BattleShips", "type" => "battleships", "page" => $page));
    }

    public function capitals($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "Capitals", "type" => "capitals", "page" => $page));
    }

    public function freighters($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "Freighters", "type" => "freighters", "page" => $page));
    }

    public function superCarriers($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "SuperCarriers", "type" => "supercarriers", "page" => $page));
    }

    public function titans($page = 0) {
        return $this->render("pages/kills.twig", array("name" => "Titans", "type" => "titans", "page" => $page));
    }
}