<?php
namespace Thessia\Model\Database;

use Thessia\Helper\Mongo;

// http://in.php.net/manual/en/mongo.sqltomongo.php
// http://mongodb.github.io/mongo-php-library/ <- Most likely to work, since sort, projection etc. isn't a part of the old docs on php.net
// Json schema validation? http://pastebin.com/K9w655D0 http://jsonschema.net/#/

/**
 * Class Killmails
 * @package Thessia\Model\Database
 */
class Killmails extends Mongo
{
    /**
     * @var string
     */
    public $collectionName = "killmails";
    /**
     * @var array
     */
    public $indexes = array(
        array("killID" => 1, "unique" => 1), // KillID ascending
        array("solarSystemID" => 1),
        array("solarSystemName" => 1),
        array("regionID" => 1),
        array("regionName" => 1),
        array("victim.characterID" => 1),
        array("victim.characterName" => 1),
        array("victim.corporationID" => 1),
        array("victim.corporationName" => 1),
        array("victim.allianceID" => 1),
        array("victim.allianceName" => 1),
        array("victim.factionID" => 1),
        array("victim.factionName" => 1),
        array("victim.shipTypeID" => 1),
        array("victim.shipTypeName" => 1),
        array("victim.damageTaken" => 1),
        array("attackers.characterID" => 1),
        array("attackers.characterName" => 1),
        array("attackers.corporationID" => 1),
        array("attackers.corporationName" => 1),
        array("attackers.allianceID" => 1),
        array("attackers.allianceName" => 1),
        array("attackers.factionID" => 1),
        array("attackers.factionName" => 1),
        array("attackers.finalBlow" => 1),
        array("attackers.weaponTypeID" => 1),
        array("attackers.shipTypeID" => 1),
        array("attackers.shipTypeName" => 1),
        array("attackers.damageDone" => 1),
        array("items.typeID" => 1),
        array("items.typeName" => 1),
        array("items.groupID" => 1)
    );

    /**
     *
     */
    public function insertKillmail()
    {
        $data = json_decode(file_get_contents("https://evedata.xyz/api/killlist/latest/"));
        foreach ($data as $kill) {
            try {
                $this->collection->insertOne($kill);
            } catch (\Exception $e) {

            }
        }
    }

    /**
     * @return \MongoDB\Driver\Cursor
     */
    public function findMail()
    {
        return $this->collection->find(
            array("attackers.characterName" => "Blitz Kriegar")
        );
    }

    /**
     * @return \MongoDB\Driver\Cursor
     */
    public function getField() {
        // I only want characterIDs from the attacker
        return $this->collection->find(
            array(),
            array(
                "projection" => array("killTime" => 1, "killID" => 1),
                "sort" => array("killTime" => -1)
            )
        );
    }
}