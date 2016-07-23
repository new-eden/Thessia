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

class Character {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
            if (isset($contractID))
                $requestArray["contractID"] = $contractID;

            $result = $p->Contracts($requestArray)->toArray();
            return $result;
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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

            if (isset($fromID))
                $requestArray["fromID"] = $fromID;

            if (isset($rowCount))
                $requestArray["rowCount"] = $rowCount;

            $result = $p->KillMails($requestArray)->toArray();
            return $result;
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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

            if (isset($orderID))
                $requestArray["orderID"] = $orderID;

            $result = $p->MarketOrders($requestArray)->toArray();
            return $result;
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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

            if (isset($fromID))
                $requestArray["fromID"] = $fromID;

            if (isset($rowCount))
                $requestArray["rowCount"] = $rowCount;


            $result = $p->WalletJournal($requestArray)->toArray();
            return $result;
        } catch (\Exception $exception) {
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

            if (isset($fromID))
                $requestArray["fromID"] = $fromID;

            if (isset($rowCount))
                $requestArray["rowCount"] = $rowCount;

            $result = $p->WalletTransactions($requestArray)->toArray();
            return $result;
        } catch (\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $characterID, $exception);
            return array();
        }
    }
}