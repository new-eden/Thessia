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

namespace Thessia\Tasks;

use MongoDB\Collection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thessia\Helper\CrestHelper;
use Thessia\Lib\Db;

class updatePrices extends Command
{
    protected function configure()
    {
        $this
            ->setName("updatePrices")
            ->setDescription("Manually updates the pricing table.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = getContainer();
        /** @var \MongoClient $mongo */
        $mongo = $container->get("mongo");
        /** @var CrestHelper $crestHelper */
        $crestHelper = $container->get("crestHelper");
        /** @var Collection $collection */
        $collection = $mongo->selectCollection("ccp", "typeIDs");
        /** @var Collection $priceCollection */
        $priceCollection = $mongo->selectCollection("thessia", "marketPrices");

        $output->writeln("Updating item values from CREST");

        // Get the Market Prices from CREST
        $marketData = $crestHelper->getMarketPrices();
        $marketData = $this->addExtraPrices($marketData);
        if(isset($marketData["items"])) {
            foreach ($marketData["items"] as $data) {
                $typeID = $data["type"]["id"];
                $typeData = $collection->find(array("typeID" => $typeID))->toArray();

                if (empty($typeData[0])) {
                    continue;
                }

                // If it's not empty, we bind typeData to typeData[0] to get the first element in the array..
                $typeData = $typeData[0];

                $priceArray = array(
                    "typeID" => (int)$typeID,
                    "typeNames" => $typeData["name"],
                    "marketGroupID" => (int)isset($typeData["marketGroupID"]) ? $typeData["marketGroupID"] : 0,
                    "groupID" => (int)$typeData["groupID"],
                    "adjustedPrice" => (int)isset($data["adjustedPrice"]) ? $data["adjustedPrice"] : 0,
                    "averagePrice" => (int)isset($data["averagePrice"]) ? $data["averagePrice"] : 0,
                    "lastUpdated" => date("Y-m-d H:i:s")
                );
                $output->writeln("CRON UpdatePrices: Updating {$typeData["name"]["en"]}");
                $priceCollection->replaceOne(array("typeID" => $typeID), $priceArray, array("upsert" => true));
            }
        }

        exit;
    }

    private function addExtraPrices(array $marketData): array {
        $container = getContainer();
        $mongo = $container->get("mongo");
        $typeIDs = $mongo->selectCollection("ccp", "typeIDs");
        $types = array(
            35834 => 300000000000, // Keepstar
            12478 => 0.01, // Khumak
            42124 => 25000000000, // Vehement
            2834 => 80000000000, // Utu
            3516 => 80000000000, // Malice
            11375 => 80000000000, // Freki
            3518 => 100000000000, // Vangel
            32788 => 100000000000, // Cambion
            32790 => 100000000000, // Etana
            32209 => 100000000000, // Mimir
            33673 => 100000000000, // Whiptail
            33397 => 120000000000, // Chremoas
            35779 => 120000000000, // Imp
            2836 => 150000000000, // Adrestia
            33675 => 150000000000, // Chameleon
            35781 => 150000000000, // Fiend
            9860 => 1000000000000, // Polaris
            11019 => 1000000000000, // Cockroach
            11940 => 500000000000, // Gold Magnate
            11942 => 500000000000, // Silver Magnate
            635 => 500000000000, // Opux Luxury Yacht
            11011 => 500000000000, // Guardian-Vexor
            25560 => 500000000000, // Opux dragoon Yacht
            13202 => 750000000000, // Megathron Federate Issue
            26840 => 750000000000, // Raven State Issue
            11936 => 750000000000, // Apocalypse Imperial Issue
            11938 => 750000000000, // Armageddon Imperial Issue
            26842 => 750000000000, // Tempest Tribal Issue
            671 => 100000000000, // Erebus
            3764 => 100000000000, // Leviathan
            11567 => 100000000000, // Avatar
            23773 => 100000000000, // Ragnarok
            42126 => 1000000000000, // Vanquisher
            3514 => 100000000000, // Revenant
            22852 => 20000000000, // Hel
            23913 => 20000000000, // Nyx
            23917 => 20000000000, // Wyvern
            23919 => 20000000000, // Aeon
            42125 => 100000000000, // Vendetta
            670 => 10000, // Capsule
            33328 => 10000, // Genolution Capsule
        );

        foreach($types as $id => $price) {
            $itemName = $typeIDs->findOne(array("typeID" => $id))["name"]["en"];
            $marketData["items"][] = array(
                "adjustedPrice" => $price,
                "averagePrice" => $price,
                "type" => array(
                    "id_str" => "{$id}",
                    "href" => "https://crest-tq.eveonline.com/inventory/types/{$id}/",
                    "id" => $id,
                    "name" => $itemName
                )
            );
        }

        return $marketData;
    }
}