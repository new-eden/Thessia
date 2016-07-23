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

class EVE {
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
     * Returns a list of alliances in eve.
     *
     * @return array
     */
    public function eveAllianceList(): array {
        try {
            $p = $this->pheal->Pheal();
            $p->scope = "Eve";
            $result = $p->AllianceList()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException(null, null, $exception);
            return array();
        }
    }

    /**
     * Returns the characterName, characterID, corporationName, corporationID, allianceName, allianceID, factionName, factionID for the given list of characterIDs.
     *
     * @param array $characterIDs
     * @return array
     */
    public function eveCharacterAffiliation($characterIDs = array()): array {
        try {
            $p = $this->pheal->Pheal();
            $p->scope = "Eve";
            $result = $p->CharacterAffiliation(array("IDs" => implode(",", $characterIDs)))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException(null, null, $exception);
            return array();
        }
    }

    /**
     * Returns the ownerID for a given character, faction, alliance or corporation name, or the typeID for other objects such as stations, solar systems, planets, etc.
     *
     * @param array $characterNames
     * @return array
     */
    public function eveCharacterID($characterNames = array()): array {
        try {
            $p = $this->pheal->Pheal();
            $p->scope = "Eve";
            $result = $p->CharacterID(array("names" => implode(",", $characterNames)))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException(null, null, $exception);
            return array();
        }
    }

    /**
     * Without a supplied API key it will return the same data as a show info call on the character would do in the client. With a limited API key it will add total skill points as well as the current ship you are in and its name. Supplied with a full API key your account balance and your last known location (cached) will be added to the return.
     *
     * @param int $characterID
     * @param int|null $apiKey
     * @param string|null $vCode
     * @return array
     */
    public function eveCharacterInfo(int $characterID, int $apiKey = null, string $vCode = null): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Eve";

            $requestArray = array("characterID" => $characterID);

            if(isset($apiKey))
                $requestArray["apiKey"] = $apiKey;

            if(isset($vCode))
                $requestArray["vCode"] = $vCode;

            $result = $p->CharacterInfo($requestArray)->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Returns the name associated with an ownerID.
     *
     * @param array $characterIDs
     * @return array
     */
    public function eveCharacterName($characterIDs = array()): array {
        try {
            $p = $this->pheal->Pheal();
            $p->scope = "Eve";
            $result = $p->CharacterName(array("IDs" => implode(",", $characterIDs)))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException(null, null, $exception);
            return array();
        }
    }

    /**
     * Returns a list of Conquerable Stations in EVE
     * @return array
     */
    public function eveConquerableStationList(): array {
        try {
            $p = $this->pheal->Pheal();
            $p->scope = "Eve";
            $result = $p->ConquerableStationList()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException(null, null, $exception);
            return array();
        }
    }

    /**
     * Returns a list of error codes that can be returned by the EVE API servers. Error types are broken into the following categories according to their first digit:
     * 1xx - user inp
     * 2xx - authentication
     * 5xx - server
     * 9xx - miscellaneous
     *
     * @return array
     */
    public function eveErrorList(): array {
        try {
            $p = $this->pheal->Pheal();
            $p->scope = "Eve";
            $result = $p->ErrorList()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException(null, null, $exception);
            return array();
        }
    }

    /**
     * Returns global stats on the factions in factional warfare including the number of pilots in each faction, the number of systems they control, and how many kills and victory points each and all factions obtained yesterday, in the last week, and total.
     *
     * @return array
     */
    public function eveFacWarStats(): array {
        try {
            $p = $this->pheal->Pheal();
            $p->scope = "Eve";
            $result = $p->FacWarStats()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException(null, null, $exception);
            return array();
        }
    }

    /**
     * Returns Factional Warfare Top 100 Stats
     *
     * @return array
     */
    public function eveFacWarTopSTats(): array {
        try {
            $p = $this->pheal->Pheal();
            $p->scope = "Eve";
            $result = $p->FacWarTopStats()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException(null, null, $exception);
            return array();
        }
    }

    /**
     * Returns a list of transaction types used in the Character/Wallet_Journal
     * https://dev.neweden.xyz/Char/WalletJournal
     *
     * @return array
     */
    public function eveRefTypes(): array {
        try {
            $p = $this->pheal->Pheal();
            $p->scope = "Eve";
            $result = $p->RefTypes()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException(null, null, $exception);
            return array();
        }
    }

    /**
     * XML of currently in-game skills (including unpublished skills).
     *
     * @return array
     */
    public function eveSkillTree(): array {
        try {
            $p = $this->pheal->Pheal();
            $p->scope = "Eve";
            $result = $p->SkillTree()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException(null, null, $exception);
            return array();
        }
    }

    /**
     * Returns the name associated with a typeID.
     *
     * @param array $typeIDs
     * @return array
     */
    public function eveTypeName($typeIDs = array()): array {
        try {
            $p = $this->pheal->Pheal();
            $p->scope = "Eve";
            $result = $p->TypeName(array("IDs" => implode(",", $typeIDs)))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException(null, null, $exception);
            return array();
        }
    }
}