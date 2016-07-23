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

class Corporation {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
            if (isset($contractID))
                $requestArray["contractID"] = $contractID;

            $result = $p->Contracts($requestArray)->toArray();
            return $result;
        } catch (\Exception $exception) {
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
            if (isset($characterID))
                $requestArray["characterID"] = $characterID;

            $result = $p->CorporationSheet($requestArray)->toArray();
            return $result;
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
            if (isset($fromID))
                $requestArray["fromID"] = $fromID;

            if (isset($rowCount))
                $requestArray["rowCount"] = $rowCount;

            $result = $p->KillMails($requestArray)->toArray();
            return $result;
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
            if (isset($orderID))
                $requestArray["orderID"] = $orderID;

            $result = $p->MarketOrders($requestArray)->toArray();
            return $result;
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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
        } catch (\Exception $exception) {
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

            if (isset($fromID))
                $requestArray["fromID"] = $fromID;

            if (isset($rowCount))
                $requestArray["rowCount"] = $rowCount;

            $result = $p->WalletJournal($requestArray)->toArray();
            return $result;
        } catch (\Exception $exception) {
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

            if (isset($fromID))
                $requestArray["fromID"] = $fromID;

            if (isset($rowCount))
                $requestArray["rowCount"] = $rowCount;

            $result = $p->WalletTransactions($requestArray)->toArray();
            return $result;
        } catch (\Exception $exception) {
            $this->pheal->handleApiException($apiKey, $vCode, $exception);
            return array();
        }
    }
}