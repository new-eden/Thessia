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

namespace Thessia\Model\Site;


use Thessia\Helper\EVE\Account;

class ApiKeyCheck
{
    /**
     * Example:
     *
     * array(
     *  "accessMask" => 1234,
     *  "type" => "Character",
     *  "name" => "Clones",
     *  "groupID" => 3,
     *  "description" => "List of your implants, attributes, jump clones, and jump fatigue timers."
     * )
     *
     * @var array $accessMasks array of arrays
     */
    private $accessMasks = array(
            array(
                "accessMask" => 2147483648,
                "type" => "Character",
                "name" => "Clones",
                "groupID" => 3,
                "description" => "List of your implants, attributes, jump clones, and jump fatigue timers.",
            ),
            array(
                "accessMask" => 1073741824,
                "type" => "Character",
                "name" => "Skills",
                "groupID" => 3,
                "description" => "List of all your skills.",
            ),
            array(
                "accessMask" => 536870912,
                "type" => "Character",
                "name" => "ChatChannels",
                "groupID" => 7,
                "description" => "List of all chat channels the character owns or is an operator of.",
            ),
            array(
                "accessMask" => 268435456,
                "type" => "Character",
                "name" => "Bookmarks",
                "groupID" => 3,
                "description" => "List of all personal bookmarks.",
            ),
            array(
                "accessMask" => 134217728,
                "type" => "Character",
                "name" => "Locations",
                "groupID" => 3,
                "description" => "Allows the fetching of coordinate and name data for items owned by the character.",
            ),
            array(
                "accessMask" => 67108864,
                "type" => "Character",
                "name" => "Contracts",
                "groupID" => 3,
                "description" => "List of all Contracts the character is involved in.",
            ),
            array(
                "accessMask" => 33554432,
                "type" => "Character",
                "name" => "AccountStatus",
                "groupID" => 3,
                "description" => "EVE player account status.",
            ),
            array(
                "accessMask" => 16777216,
                "type" => "Character",
                "name" => "CharacterInfo",
                "groupID" => 3,
                "description" => "Sensitive Character Information, exposes account balance and last known location on top of the other Character Information call.",
            ),
            array(
                "accessMask" => 8388608,
                "type" => "Character",
                "name" => "CharacterInfo",
                "groupID" => 4,
                "description" => "Character information, exposes skill points and current ship information on top of Show Info information.",
            ),
            array(
                "accessMask" => 4194304,
                "type" => "Character",
                "name" => "WalletTransactions",
                "groupID" => 1,
                "description" => "Market transaction journal of character.",
            ),
            array(
                "accessMask" => 2097152,
                "type" => "Character",
                "name" => "WalletJournal",
                "groupID" => 1,
                "description" => "Wallet journal of character.",
            ),
            array(
                "accessMask" => 1048576,
                "type" => "Character",
                "name" => "UpcomingCalendarEvents",
                "groupID" => 3,
                "description" => "Upcoming events on characters calendar.",
            ),
            array(
                "accessMask" => 524288,
                "type" => "Character",
                "name" => "Standings",
                "groupID" => 4,
                "description" => "NPC Standings towards the character.",
            ),
            array(
                "accessMask" => 262144,
                "type" => "Character",
                "name" => "SkillQueue",
                "groupID" => 3,
                "description" => "Entire skill queue of character.",
            ),
            array(
                "accessMask" => 131072,
                "type" => "Character",
                "name" => "SkillInTraining",
                "groupID" => 3,
                "description" => "Skill currently in training on the character. Subset of entire Skill Queue.",
            ),
            array(
                "accessMask" => 65536,
                "type" => "Character",
                "name" => "Research",
                "groupID" => 2,
                "description" => "List of all Research agents working for the character and the progress of the research.",
            ),
            array(
                "accessMask" => 32768,
                "type" => "Character",
                "name" => "NotificationTexts",
                "groupID" => 7,
                "description" => "Actual body of notifications sent to the character. Requires Notification access to function.",
            ),
            array(
                "accessMask" => 16384,
                "type" => "Character",
                "name" => "Notifications",
                "groupID" => 7,
                "description" => "List of recent notifications sent to the character.",
            ),
            array(
                "accessMask" => 8192,
                "type" => "Character",
                "name" => "Medals",
                "groupID" => 4,
                "description" => "Medals awarded to the character.",
            ),
            array(
                "accessMask" => 4096,
                "type" => "Character",
                "name" => "MarketOrders",
                "groupID" => 1,
                "description" => "List of all Market Orders the character has made.",
            ),
            array(
                "accessMask" => 2048,
                "type" => "Character",
                "name" => "MailMessages",
                "groupID" => 7,
                "description" => "List of all messages in the characters EVE Mail Inbox.",
            ),
            array(
                "accessMask" => 1024,
                "type" => "Character",
                "name" => "MailingLists",
                "groupID" => 7,
                "description" => "List of all Mailing Lists the character subscribes to.",
            ),
            array(
                "accessMask" => 512,
                "type" => "Character",
                "name" => "MailBodies",
                "groupID" => 7,
                "description" => "EVE Mail bodies. Requires MailMessages as well to function.",
            ),
            array(
                "accessMask" => 256,
                "type" => "Character",
                "name" => "KillLog",
                "groupID" => 4,
                "description" => "Characters kill log.",
            ),
            array(
                "accessMask" => 128,
                "type" => "Character",
                "name" => "IndustryJobs",
                "groupID" => 2,
                "description" => "Character jobs, completed and active.",
            ),
            array(
                "accessMask" => 64,
                "type" => "Character",
                "name" => "FacWarStats",
                "groupID" => 4,
                "description" => "Characters Factional Warfare Statistics.",
            ),
            array(
                "accessMask" => 32,
                "type" => "Character",
                "name" => "ContactNotifications",
                "groupID" => 7,
                "description" => "Most recent contact notifications for the character.",
            ),
            array(
                "accessMask" => 16,
                "type" => "Character",
                "name" => "ContactList",
                "groupID" => 7,
                "description" => "List of character contacts and relationship levels.",
            ),
            array(
                "accessMask" => 8,
                "type" => "Character",
                "name" => "CharacterSheet",
                "groupID" => 3,
                "description" => "Character Sheet information. Contains basic Show Info information along with clones, account balance, implants, attributes, skills, certificates and corporation roles.",
            ),
            array(
                "accessMask" => 4,
                "type" => "Character",
                "name" => "CalendarEventAttendees",
                "groupID" => 3,
                "description" => "Event attendee responses. Requires UpcomingCalendarEvents to function.",
            ),
            array(
                "accessMask" => 2,
                "type" => "Character",
                "name" => "AssetList",
                "groupID" => 3,
                "description" => "Entire asset list of character.",
            ),
            array(
                "accessMask" => 1,
                "type" => "Character",
                "name" => "AccountBalance",
                "groupID" => 1,
                "description" => "Current balance of characters wallet.",
            ),
            array(
                "accessMask" => 67108864,
                "type" => "Corporation",
                "name" => "Bookmarks",
                "groupID" => 3,
                "description" => "List of all corporate bookmarks.",
            ),
            array(
                "accessMask" => 33554432,
                "type" => "Corporation",
                "name" => "MemberTrackingExtended",
                "groupID" => 5,
                "description" => "Extensive Member information. Time of last logoff, last known location and ship.",
            ),
            array(
                "accessMask" => 16777216,
                "type" => "Corporation",
                "name" => "Locations",
                "groupID" => 3,
                "description" => "Allows the fetching of coordinate and name data for items owned by the corporation.",
            ),
            array(
                "accessMask" => 8388608,
                "type" => "Corporation",
                "name" => "Contracts",
                "groupID" => 3,
                "description" => "List of recent Contracts the corporation is involved in.",
            ),
            array(
                "accessMask" => 4194304,
                "type" => "Corporation",
                "name" => "Titles",
                "groupID" => 5,
                "description" => "Titles of corporation and the roles they grant.",
            ),
            array(
                "accessMask" => 2097152,
                "type" => "Corporation",
                "name" => "WalletTransactions",
                "groupID" => 1,
                "description" => "Market transactions of all corporate accounts.",
            ),
            array(
                "accessMask" => 1048576,
                "type" => "Corporation",
                "name" => "WalletJournal",
                "groupID" => 1,
                "description" => "Wallet journal for all corporate accounts.",
            ),
            array(
                "accessMask" => 524288,
                "type" => "Corporation",
                "name" => "StarbaseList",
                "groupID" => 6,
                "description" => "List of all corporate starbases.",
            ),
            array(
                "accessMask" => 262144,
                "type" => "Corporation",
                "name" => "Standings",
                "groupID" => 4,
                "description" => "NPC Standings towards corporation.",
            ),
            array(
                "accessMask" => 131072,
                "type" => "Corporation",
                "name" => "StarbaseDetail",
                "groupID" => 6,
                "description" => "List of all settings of corporate starbases.",
            ),
            array(
                "accessMask" => 65536,
                "type" => "Corporation",
                "name" => "Shareholders",
                "groupID" => 1,
                "description" => "Shareholders of the corporation.",
            ),
            array(
                "accessMask" => 32768,
                "type" => "Corporation",
                "name" => "OutpostServiceDetail",
                "groupID" => 6,
                "description" => "List of all service settings of corporate outposts.",
            ),
            array(
                "accessMask" => 16384,
                "type" => "Corporation",
                "name" => "OutpostList",
                "groupID" => 6,
                "description" => "List of all outposts controlled by the corporation.",
            ),
            array(
                "accessMask" => 8192,
                "type" => "Corporation",
                "name" => "Medals",
                "groupID" => 4,
                "description" => "List of all medals created by the corporation.",
            ),
            array(
                "accessMask" => 4096,
                "type" => "Corporation",
                "name" => "MarketOrders",
                "groupID" => 1,
                "description" => "List of all corporate market orders.",
            ),
            array(
                "accessMask" => 2048,
                "type" => "Corporation",
                "name" => "MemberTrackingLimited",
                "groupID" => 5,
                "description" => "Limited Member information.",
            ),
            array(
                "accessMask" => 1024,
                "type" => "Corporation",
                "name" => "MemberSecurityLog",
                "groupID" => 5,
                "description" => "Member role and title change log.",
            ),
            array(
                "accessMask" => 512,
                "type" => "Corporation",
                "name" => "MemberSecurity",
                "groupID" => 5,
                "description" => "Member roles and titles.",
            ),
            array(
                "accessMask" => 256,
                "type" => "Corporation",
                "name" => "KillLog",
                "groupID" => 4,
                "description" => "Corporation kill log.",
            ),
            array(
                "accessMask" => 128,
                "type" => "Corporation",
                "name" => "IndustryJobs",
                "groupID" => 2,
                "description" => "Corporation jobs, completed and active.",
            ),
            array(
                "accessMask" => 64,
                "type" => "Corporation",
                "name" => "FacWarStats",
                "groupID" => 4,
                "description" => "Corporations Factional Warfare Statistics.",
            ),
            array(
                "accessMask" => 32,
                "type" => "Corporation",
                "name" => "ContainerLog",
                "groupID" => 3,
                "description" => "Corporate secure container access log.",
            ),
            array(
                "accessMask" => 16,
                "type" => "Corporation",
                "name" => "ContactList",
                "groupID" => 7,
                "description" => "Corporate contact list and relationships.",
            ),
            array(
                "accessMask" => 8,
                "type" => "Corporation",
                "name" => "CorporationSheet",
                "groupID" => 3,
                "description" => "Exposes basic Show Info information as well as Member Limit and basic division and wallet info.",
            ),
            array(
                "accessMask" => 4,
                "type" => "Corporation",
                "name" => "MemberMedals",
                "groupID" => 5,
                "description" => "List of medals awarded to corporation members.",
            ),
            array(
                "accessMask" => 2,
                "type" => "Corporation",
                "name" => "AssetList",
                "groupID" => 3,
                "description" => "List of all corporation assets.",
            ),
            array(
                "accessMask" => 1,
                "type" => "Corporation",
                "name" => "AccountBalance",
                "groupID" => 1,
                "description" => "Current balance of all corporation accounts.",
            ),
    );

    /**
     * @var Account
     */
    private $keyInfo;

    /**
     * ApiKeyCheck constructor.
     */
    public function __construct() {
        $container = getContainer();
        $this->keyInfo = $container->get("ccpAccount");
    }

    public function checkApiKey(int $apiKey, string $vCode) {
        $keyInfo = $this->keyInfo->accountAPIKeyInfo($apiKey, $vCode);
        var_dump($keyInfo);
    }
}