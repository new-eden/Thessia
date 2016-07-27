<?php
namespace PHPSTORM_META {

    use League\Container\Container;

    $STATIC_METHOD_TYPES = [
        \Interop\Container\ContainerInterface::get('') => [
          'config' instanceof Thessia\Lib\Config,
          'log' instanceof Monolog\Logger,
          'timer' instanceof Thessia\Lib\Timer,
          'cache' instanceof Thessia\Lib\Cache,
          'db' instanceof Thessia\Lib\Db,
          'session' instanceof Thessia\Lib\SessionHandler,
          'mongo' instanceof MongoDB\Client,
          'killmails' instanceof Thessia\Model\Database\EVE\Killmails,
          'blueprints' instanceof Thessia\Model\Database\CCP\blueprints,
          'categoryIDs' instanceof Thessia\Model\Database\CCP\categoryIDs,
          'certificates' instanceof Thessia\Model\Database\CCP\certificates,
          'constellations' instanceof Thessia\Model\Database\CCP\constellations,
          'graphicIDs' instanceof Thessia\Model\Database\CCP\graphicIDs,
          'groupIDs' instanceof Thessia\Model\Database\CCP\groupIDs,
          'iconIDs' instanceof Thessia\Model\Database\CCP\iconIDs,
          'landmarks' instanceof Thessia\Model\Database\CCP\landmarks,
          'regions' instanceof Thessia\Model\Database\CCP\regions,
          'skinLicenses' instanceof Thessia\Model\Database\CCP\skinLicenses,
          'skinMaterials' instanceof Thessia\Model\Database\CCP\skinMaterials,
          'skins' instanceof Thessia\Model\Database\CCP\skins,
          'solarSystems' instanceof Thessia\Model\Database\CCP\solarSystems,
          'tournamentRuleSets' instanceof Thessia\Model\Database\CCP\tournamentRuleSets,
          'typeIDs' instanceof Thessia\Model\Database\CCP\typeIDs,
          'crest' instanceof Thessia\Model\EVE\Crest,
          'curl' instanceof Thessia\Lib\cURL,
          'prices' instanceof Thessia\Model\Database\EVE\Prices,
          'alliances' instanceof Thessia\Model\Database\EVE\Alliances,
          'corporations' instanceof Thessia\Model\Database\EVE\Corporations,
          'characters' instanceof Thessia\Model\Database\EVE\Characters,
          'participants' instanceof Thessia\Model\Database\EVE\Participants,
          'pheal' instanceof Thessia\Helper\Pheal,
          'ccpAccount' instanceof Thessia\Helper\EVEApi\Account,
          'ccpAPI' instanceof Thessia\Helper\EVEApi\API,
          'ccpCharacter' instanceof Thessia\Helper\EVEApi\Character,
          'ccpCorporation' instanceof Thessia\Helper\EVEApi\Corporation,
          'ccpEVE' instanceof Thessia\Helper\EVEApi\EVE,
          'ccpMap' instanceof Thessia\Helper\EVEApi\Map,
          'ccpServer' instanceof Thessia\Helper\EVEApi\Server,
          'render' instanceof \Thessia\Lib\Render,
          "" == "@"
        ]
    ];
}