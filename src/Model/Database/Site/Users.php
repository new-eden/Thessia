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

namespace Thessia\Model\Database\Site;


use Thessia\Helper\Mongo;

/**
 * Class Settings
 * @package Thessia\Model\Database\Site
 */
class Users extends Mongo
{
    /**
     * The name of the models collection
     */
    public $collectionName = 'users';

    /**
     * The name of the database the collection is stored in
     */
    public $databaseName = 'thessia';

    /**
     * An array of indexes for this collection
     */
    public $indexes = array(
        array(
            "key" => array("characterID" => -1),
            "unique" => true
        )
    );

    // Example data
    /*array(
        "characterID" => 0,
        "characterName" => "",
        "corporationID" => 0,
        "corporationName" => "",
        "allianceID" => 0,
        "allianceName" => "",
        "refreshToken" => "",
        "refreshTokenLastUpdated" => 0,
        "scopes" => array(
            "scope1",
            "scope2",
        )
    )*/
    public function getCharacterInfo(int $characterID): array {
        return array();
    }

    public function getByCharacterID(int $characterID): array {
        return array();
    }

    public function getByCorporationID(int $corporationID): array {
        return array();
    }

    public function getByAllianceID(int $allianceID): array {
        return array();
    }

    public function getRefreshTokenByCharacterID(int $characterID): array {
        return array();
    }

    public function getByScope(string $scopeType): array {
        $validScopes = array(
            "characterAccountRead",
            "characterFittingsRead",
            "characterFittingsWrite",
            "characterKillsRead",
            "characterMailRead",
            "characterSkillsRead",
            "characterStatsRead",
            "corporationKillsRead",
            "publicData",
            "esi-killmails.read_killmails.v1",
            "esi-skills.read_skills.v1",
            "esi-corporations.read_corporation_membership.v1"
        );

        return array();
    }

    public function insertUser(array $replace = array(), array $data = array()): bool {
        return true;
    }
}