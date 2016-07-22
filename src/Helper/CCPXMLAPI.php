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
 * Time: 01:40
 */

namespace Thessia\Helper;


use Thessia\Lib\Cache;
use Thessia\Model\Site\Storage;

/**
 * Class CCPXMLAPI
 * @package Thessia\Helper
 */
class CCPXMLAPI
{
    /**
     * @var Pheal
     */
    private $pheal;
    /**
     * @var Storage
     */
    private $storage;
    /**
     * @var Cache
     */
    private $cache;

    /**
     * CCPXMLAPI constructor.
     * @param Pheal $pheal
     * @param Storage $storage
     * @param Cache $cache
     */
    public function __construct(Pheal $pheal, Storage $storage, Cache $cache) {
        $this->pheal = $pheal;
        $this->storage = $storage;
        $this->cache = $cache;
    }

    // API Endpoint
    /**
     * Returns a list of the API Calls that contain private Character or Corporation information and which access bits are required.
     *
     * @return array
     */
    public function apiCallList(): array {
        try {
            $p = $this->pheal->Pheal();
            $p->scope = "API";
            $result = $p->CallList()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException(null, null, $exception);
            return array();
        }
    }

    // Account Endpoint
    /**
     * Returns basic account information including when the subscription lapses, total play time in minutes, total times logged on and date of account creation. In the case of game time code accounts it will also look for available offers of time codes.
     *
     * @param int $apiKey
     * @param string $vCode
     * @return array
     */
    public function accountAPIKeyInfo(int $apiKey, string $vCode): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Account";
            $result = $p->APIKeyInfo()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, null, $exception);
            return array();
        }
    }

    /**
     * Returns information about the API key and a list of the characters exposed by it.
     *
     * @param int $apiKey
     * @param string $vCode
     * @return array
     */
    public function accountAccountStatus(int $apiKey, string $vCode): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Account";
            $result = $p->accountStatus()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, null, $exception);
            return array();
        }
    }

    /**
     * Returns a list of all characters on an account.
     *
     * @param int $apiKey
     * @param string $vCode
     * @return array
     */
    public function accountCharacters(int $apiKey, string $vCode): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Account";
            $result = $p->Characters()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, null, $exception);
            return array();
        }
    }

    // Character Endpoint
    /**
     * Returns the ISK balance of a character.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function characterAccountBalance(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->AccountBalance(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns a list of assets owned by a character.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function characterAssetList(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->AssetList(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Return a list of blueprints owned by a character
     * Returns a max of 200k rows.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function characterBlueprints(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->Blueprints(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns a list of all invited attendees for a given event.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param array $eventIDs
     * @return array
     */
    public function characterCalendarEventAttendees(int $apiKey, string $vCode, int $characterID, $eventIDs = array()): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->CalendarEventAttendees(array("characterID" => $characterID, "eventIDs" => implode(",", $eventIDs)))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns attributes relating to a specific character.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int|null $characterID
     * @return array
     */
    public function characterCharacterSheet(int $apiKey, string $vCode, int $characterID = null): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->CharacterSheet(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns the character's contact and watch lists, incl. agents and respective standings set by the character. Also includes that character's corporation and/or alliance contacts.
     * See the Standings API for standings towards the character from agents and NPC entities.
     * https://dev.neweden.xyz/Char/Standings
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function characterContactList(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->ContactList(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Lists the notifications received about having been added to someone's contact list.
     * https://dev.neweden.xyz/Char/Notifications
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function characterContactNotifications(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->ContactNotifications(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Lists the latest bids that have been made to any recent auctions.
     * More info here: http://www.eveonline.com/devblog.asp?a=blog&nbid=2383
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function characterContractBids(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->ContractBids(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Lists items that a specified contract contains.
     * More info here: http://www.eveonline.com/devblog.asp?a=blog&nbid=2383
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param int $contractID
     * @return array
     */
    public function characterContractItems(int $apiKey, string $vCode, int $characterID, int $contractID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->ContractItems(array("characterID" => $characterID, "contractID" => $contractID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Lists the personal contracts for a character.
     * More info here: http://www.eveonline.com/devblog.asp?a=blog&nbid=2383
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param int|null $contractID
     * @return array
     */
    public function characterContracts(int $apiKey, string $vCode, int $characterID, int $contractID = null): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";

            $requestArray = array("characterID" => $characterID);
            if(isset($contractID))
                $requestArray["contractID"] = $contractID;

            $result = $p->Contracts($requestArray)->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * If the character is enlisted in Factional Warfare, this will return the faction the character is enlisted in, the current rank and the highest rank the character ever had attained, and how many kills and victory points the character obtained yesterday, in the last week, and total. Otherwise returns an error code.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function characterFacWarStats(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->FacWarStats(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns industry jobs currently running, and that has been running within the last 90 days.
     * Returns a max of 10k rows and 90 days worth of data.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function characterIndustryJobs(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->IndustryJobs(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns industry jobs currently running, and that has been running within the last 90 days.
     * Returns a max of 10k rows and 90 days worth of data.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function characterIndustryJobsHistory(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->IndustryJobsHistory(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns a list of killmails done by the character.
     * Returns the most recent 100. Use $fromID to scroll back.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param int|null $fromID
     * @param int|null $rowCount
     * @return array
     */
    public function characterKillMails(int $apiKey, string $vCode, int $characterID, int $fromID = null, int $rowCount = null): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";

            $requestArray = array("characterID" => $characterID);

            if(isset($fromID))
                $requestArray["fromID"] = $fromID;

            if(isset($rowCount))
                $requestArray["rowCount"] = $rowCount;

            $result = $p->KillMails($requestArray)->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Call will return the items name (or its type name if no user defined name exists) as well as their x,y,z coordinates. Coordinates should all be 0 for valid locations located inside of stations.
     * These can ONLY be valid locationIDs (that is items which have meaningful location coordinates. (0, 0, 0) returned for items in hangars that could have valid coordinates) and must be owned by the character or corporation associated with the key. Any attempts at illegal input will result in a completely empty return. This is done to avoid any sort of scraping or giving out more information than is appropriate (f.e. validating that a given itemID is indeed a valid location but does not belong to the owner associated with the key). To differentiate this form of invalid input from other invalid input error code 135 will be thrown for this error.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param array $IDs
     * @return array
     */
    public function characterLocations(int $apiKey, string $vCode, int $characterID, $IDs = array()): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->Locations(array("characterID" => $characterID, "IDs" => implode(",", $IDs)))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * This API call is available on TQ since Tyrannis 1.2.
     * Returns the bodies of headers that have already been fetched with the Character/Mail_Messages_(Headers) call. It will also return a list of missing IDs that could not be accessed. Bodies cannot be accessed if you have not called for their headers recently.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param array $IDs
     * @return array
     */
    public function characterMailBodies(int $apiKey, string $vCode, int $characterID, $IDs = array()): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->MailBodies(array("characterID" => $characterID, "IDs" => implode(",", $IDs)))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns the message headers for mail.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function characterMailMessages(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->MailMessages(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns an XML document listing all mailing lists the character is currently a member of.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function characterMailingLists(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->MailingLists(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns a list of market orders for your character.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param int|null $orderID
     * @return array
     */
    public function characterMarketOrders(int $apiKey, string $vCode, int $characterID, int $orderID = null): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";

            $requestArray = array("characterID" => $characterID);

            if(isset($orderID))
                $requestArray["orderID"] = $orderID;

            $result = $p->MarketOrders($requestArray)->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns a list of medals the character has.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function characterMedals(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->Medals(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns the message bodies for notifications. Headers need to be requested via Character/Notifications first.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param array $IDs
     * @return array
     */
    public function characterNotificationTexts(int $apiKey, string $vCode, int $characterID, $IDs = array()): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->NotificationTexts(array("characterID" => $characterID, "IDs" => implode(",", $IDs)))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns the message headers for notifications.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function characterNotifications(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->Notifications(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns the list of Planetary Colonies.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param int $planetID
     * @return array
     */
    public function characterPlanetaryColonies(int $apiKey, string $vCode, int $characterID, int $planetID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->PlanetaryColonies(array("characterID" => $characterID, "planetID" => $planetID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns the list of Planetary Links from one Colony.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param int $planetID
     * @return array
     */
    public function characterPlanetaryLinks(int $apiKey, string $vCode, int $characterID, int $planetID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->PlanetaryLinks(array("characterID" => $characterID, "planetID" => $planetID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns the list of Planetary Pins from one Colony.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param int $planetID
     * @return array
     */
    public function characterPlanetaryPins(int $apiKey, string $vCode, int $characterID, int $planetID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->PlanetaryPins(array("characterID" => $characterID, "planetID" => $planetID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns the list of Planetary Routes from one Colony.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param int $planetID
     * @return array
     */
    public function characterPlanetaryRoutes(int $apiKey, string $vCode, int $characterID, int $planetID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->PlanetaryRoutes(array("characterID" => $characterID, "planetID" => $planetID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns information about agents character is doing research with.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function characterResearch(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->Research(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns XML document of the skill the character is currently training.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function characterSkillInTraining(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->SkillInTraining(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns XML document containing the skill queue of the character.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function characterSkillQueue(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->SkillQueue(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns the standings towards a character from agents, NPC corporations and factions.
     * Since Tyrannis no longer provides standings towards characters or entities, use the Character/Contact_List instead.
     * https://dev.neweden.xyz/Char/ContactList
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function characterStandings(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->Standings(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns a list of all upcoming calendar events for a given character.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function characterUpcomingCalendarEvents(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";
            $result = $p->UpcomingCalendarEvents(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns a list of journal transactions for character.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param int $accountKey
     * @param int|null $fromID
     * @param int|null $rowCount
     * @return array
     */
    public function characterWalletJournal(int $apiKey, string $vCode, int $characterID, int $accountKey = 1000, int $fromID = null, int $rowCount = null): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";

            $requestArray = array("characterID" => $characterID, "accountKey" => $accountKey);

            if(isset($fromID))
                $requestArray["fromID"] = $fromID;

            if(isset($rowCount))
                $requestArray["rowCount"] = $rowCount;


            $result = $p->WalletJournal($requestArray)->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    /**
     * Returns market transactions for a character.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param int $accountKey
     * @param int|null $fromID
     * @param int|null $rowCount
     * @return array
     */
    public function characterWalletTransactions(int $apiKey, string $vCode, int $characterID, int $accountKey = 1000, int $fromID = null, int $rowCount = null): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Char";

            $requestArray = array("characterID" => $characterID, "accountKey" => $accountKey);

            if(isset($fromID))
                $requestArray["fromID"] = $fromID;

            if(isset($rowCount))
                $requestArray["rowCount"] = $rowCount;

            $result = $p->WalletTransactions($requestArray)->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }

    // Corporation Endpoint
    /**
     * Returns the ISK balance of a corporation.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function corporationAccountBalance(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->AccountBalance(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Returns a list of assets owned by a corporation.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function corporationAssetList(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->AssetList(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Returns a list of blueprints owned by a corporation.
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function corporationBlueprints(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->Blueprints(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Returns the corporation and the alliance contact lists. This is accessible by any character in any corporation.
     * This call gives standings that the corp has set towards other characters and entities. For standings that others have set towards the corp, see the Standings API.
     *https://dev.neweden.xyz/Corp/Standings
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function corporationContactList(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->ContactList(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Shows corp container audit log.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function corporationContainerLog(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->ContainerLog(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Lists the latest bids that have been made to any recent auctions.
     * More informations here : http://www.eveonline.com/devblog.asp?a=blog&nbid=2383
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function corporationContractBids(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->ContractBids(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Lists items that a specified contract contains, use the contractID parameter to specify the contract. See Char/ContractItems for details on this API call.
     * https://dev.neweden.xyz/Char/ContractItems
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param int $contractID
     * @return array
     */
    public function corporationContractItems(int $apiKey, string $vCode, int $characterID, int $contractID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->ContractItems(array("characterID" => $characterID, "contractID" => $contractID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Lists contracts issued within the last month as well as all contracts marked as outstanding or in-progress. See Character/Contracts for details on this API call.
     * https://dev.neweden.xyz/Char/Contracts
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param int|null $contractID
     * @return array
     */
    public function corporationContracts(int $apiKey, string $vCode, int $characterID, int $contractID = null): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";

            $requestArray = array("characterID" => $characterID);
            if(isset($contractID))
                $requestArray["contractID"] = $contractID;

            $result = $p->Contracts($requestArray)->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Returns attributes relating to a specific corporation.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int|null $characterID
     * @return array
     */
    public function corporationCorporationSheet(int $apiKey, string $vCode, int $characterID = null): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";

            $requestArray = array();
            if(isset($characterID))
                $requestArray["characterID"] = $characterID;

            $result = $p->CorporationSheet($requestArray)->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Returns attributes of player owned customs offices (POCOs) owned by a corporation.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function corporationCustomsOffices(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->CustomsOffices(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * If the corporation is enlisted in Factional Warfare, this will return the faction the corporation is enlisted in, the current rank and the highest rank the corporation ever had attained, and how many kills and victory points the corporation obtained yesterday, in the last week, and total. Otherwise returns an error code (125).
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function corporationFacWarStats(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->FacWarStats(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * @param int $apiKey
     * @param string $vCode
     * @return array
     */
    public function corporationFacilities(int $apiKey, string $vCode): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->Facilities()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function corporationIndustryJobs(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->IndustryJobs(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function corporationIndustryJobsHistory(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->IndustryJobsHistory(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * This is the exact same input and output for corporations as for characters. Please see the Character/KillMails API call.
     * For corporations, no character/account ID is required.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int|null $fromID
     * @param int|null $rowCount
     * @return array
     */
    public function corporationKillMails(int $apiKey, string $vCode, int $fromID = null, int $rowCount = null): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";

            $requestArray = array();
            if(isset($fromID))
                $requestArray["fromID"] = $fromID;

            if(isset($rowCount))
                $requestArray["rowCount"] = $rowCount;

            $result = $p->KillMails($requestArray)->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Call will return the items name (or its type name if no user defined name exists) as well as their x,y,z coordinates. Coordinates should all be 0 for valid locations located inside of stations. See Character/Locations for details on this API call.
     * https://dev.neweden.xyz/Char/Locations
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param array $IDs
     * @return array
     */
    public function corporationLocations(int $apiKey, string $vCode, int $characterID, $IDs = array()): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->Locations(array("characterID" => $characterID, "IDs" => implode(",", $IDs)))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Returns a list of market orders that are either not expired or have expired in the past week (at most).
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param int|null $orderID
     * @return array
     */
    public function corporationMarketOrders(int $apiKey, string $vCode, int $characterID, int $orderID = null): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";

            $requestArray = array("characterID" => $characterID);
            if(isset($orderID))
                $requestArray["orderID"] = $orderID;

            $result = $p->MarketOrders($requestArray)->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Returns a list of medals created by this corporation.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function corporationMedals(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->Medals(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Returns a list of medals issued to members.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function corporationMemberMedals(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->MemberMedals(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Returns the security roles of members in a corporation.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function corporationMemberSecurity(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->MemberSecurity(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Returns info about corporation role changes for members and who did it.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function corporationMemberSecurityLog(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->MemberSecurityLog(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * For player corps this returns the member list (same as in game). Otherwise returns an error code (207).
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $extended
     * @return array
     */
    public function corporationMemberTracking(int $apiKey, string $vCode, int $extended = 0): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->MemberTracking(array("extended" => $extended))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Outposts This new Outpost API will allow you to pull information about the corporation’s outposts, which will require a full API key from the a director(or CEO) of the corporation which the outpost belongs to. /corp/OutpostList.xml.aspx stationID, ownerID, stationName, solarSystemID, dockingCostPerShipVolume, officeRentalCost, stationTypeID, reprocessingEfficiency, reprocessingStationTake, standingOwnerID Just like in the Starbase API, an ownerID tag is present, which determines the entity which the standings are derived from.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function corporationOutpostList(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->OutpostList(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * This new Outpost API will allow you to pull information about the corporation’s outposts, which will require a full API key from the a director(or CEO) of the corporation which the outpost belongs to. /corp/OutpostServiceDetail.xml.aspx?itemID= stationed, ownerID, serviceName, minStanding, surchargePerBadStanding, discountPerGoodStanding Two things are important to note here: Just like in the Starbase API, an ownerID tag is present, which determines the entity which the standings are derived from. In the OutpostServiceDetail.xml.aspx API, if a service has default values, they will not be shown.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param int $itemID
     * @return array
     */
    public function corporationOutpostServiceDetail(int $apiKey, string $vCode, int $characterID, int $itemID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->OutpostServiceDetail(array("characterID" => $characterID, "itemID" => $itemID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Returns the character and corporation share holders of a corporation.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function corporationShareholders(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->Shareholders(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Returns the standings from NPC corporations and factions as well as agents. Since Tyrannis no longer provides standings towards characters or entities, use the Contact List API instead.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function corporationStandings(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->Standings(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Shows the settings and fuel status of a POS.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $itemID
     * @return array
     */
    public function corporationStarBaseDetail(int $apiKey, string $vCode, int $itemID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->StarbaseDetail(array("itemID" => $itemID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Shows the list and states of POS'es.
     *
     * @param int $apiKey
     * @param string $vCode
     * @return array
     */
    public function corporationStarBaseList(int $apiKey, string $vCode): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->StarbaseList()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Returns the titles of a corporation.
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @return array
     */
    public function corporationTitles(int $apiKey, string $vCode, int $characterID): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";
            $result = $p->Titles(array("characterID" => $characterID))->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Returns a list of journal transactions for corporation. See /Char/WalletJournal.xml.aspx for details on this API call.
     * https://dev.neweden.xyz/Char/WalletJournal
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param int $accountKey
     * @param int|null $fromID
     * @param int|null $rowCount
     * @return array
     */
    public function corporationWalletJournal(int $apiKey, string $vCode, int $characterID, int $accountKey = 1000, int $fromID = null, int $rowCount = null): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";

            $requestArray = array("characterID" => $characterID, "accountKey" => $accountKey);

            if(isset($fromID))
                $requestArray["fromID"] = $fromID;

            if(isset($rowCount))
                $requestArray["rowCount"] = $rowCount;

            $result = $p->WalletJournal($requestArray)->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    /**
     * Returns a list of market transactions for a corporation. See Character/Wallet Transactions for details on this API call.
     * https://dev.neweden.xyz/Char/WalletTransactions
     *
     * @param int $apiKey
     * @param string $vCode
     * @param int $characterID
     * @param int $accountKey
     * @param int|null $fromID
     * @param int|null $rowCount
     * @return array
     */
    public function corporationWalletTransactions(int $apiKey, string $vCode, int $characterID, int $accountKey = 1000, int $fromID = null, int $rowCount = null): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Corp";

            $requestArray = array("characterID" => $characterID, "accountKey" => $accountKey);

            if(isset($fromID))
                $requestArray["fromID"] = $fromID;

            if(isset($rowCount))
                $requestArray["rowCount"] = $rowCount;

            $result = $p->WalletTransactions($requestArray)->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }

    // EVE Endpoints
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

    // Map Endpoint
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


    // Server Endpoint
    /**
     * Returns current Tranquility status and number of players online.
     *
     * @return array
     */
    public function serverServerStatus(): array {
        try {
            $p = $this->pheal->Pheal();
            $p->scope = "Server";
            $result = $p->ServerStatus()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException(null, null, $exception);
            return array();
        }
    }
}