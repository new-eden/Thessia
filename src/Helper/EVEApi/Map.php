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

namespace Thessia\Helper\EVEApi;


use Thessia\Helper\Pheal;

class Map {
    /**
     * @var Pheal
     */
    private $pheal;

    /**
     * API constructor.
     * @param Pheal $pheal
     */
    public function __construct(Pheal $pheal) {
        $this->pheal = $pheal;
    }

    /**
     * Returns a list of contestable solarsystems and the NPC faction currently occupying them. It should be noted that this file only returns a non-zero ID if the occupying faction is not the sovereign faction.
     *
     * @return array
     */
    public function mapFacWarSystems(): array {
        try {
            $p = $this->pheal->Pheal();
            $p->scope = "Map";
            $result = $p->FacWarSystems()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException(null, null, $exception);
            return array();
        }
    }

    /**
     * Note that only systems with jumps are shown, if the system has no jumps, it's not listed.
     *
     * @return array
     */
    public function mapJumps(): array {
        try {
            $p = $this->pheal->Pheal();
            $p->scope = "Map";
            $result = $p->Jumps()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException(null, null, $exception);
            return array();
        }
    }

    /**
     * Returns the number of kills in solarsystems within the last hour. Only solar system where kills have been made are listed, so assume zero in case the system is not listed.
     * Wormhole systems removed from this api call since 2014/06/03 .
     *
     * @return array
     */
    public function mapKills(): array {
        try {
            $p = $this->pheal->Pheal();
            $p->scope = "Map";
            $result = $p->Kills()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException(null, null, $exception);
            return array();
        }
    }

    /**
     * Returns a list of solarsystems and what faction or alliance controls them.
     *
     * @return array
     */
    public function mapSovereignty(): array {
        try {
            $p = $this->pheal->Pheal();
            $p->scope = "Map";
            $result = $p->Sovereignty()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException(null, null, $exception);
            return array();
        }
    }
}