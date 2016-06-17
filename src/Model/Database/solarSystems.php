<?php
namespace Thessia\Model\Database;

use Thessia\Helper\Mongo;

/**
 */
class solarSystems extends Mongo
{

    /**
     * The name of the models collection
     */
    public $collectionName = 'solarSystems';

    /**
     * The name of the database the collection is stored in
     */
    public $databaseName = 'ccp';

    /**
     * An array of indexes for this collection
     */
    public $indexes = array();

    /**
     * @param mixed $constellationID
     */
    public function getAllByConstellationID($constellationID)
    {
        return $this->collection->find(
            array("constellationID" => $constellationID)
        );
    }

    /**
     * @param mixed $fieldName
     */
    public function getAllByConstellationName($fieldName)
    {
        return $this->collection->find(
            array("constellationName" => $fieldName)
        );
    }

    /**
     * @param mixed $corridor
     */
    public function getAllByCorridor($corridor)
    {
        return $this->collection->find(
            array("corridor" => $corridor)
        );
    }

    /**
     * @param mixed $regionID
     */
    public function getAllByRegionID($regionID)
    {
        return $this->collection->find(
            array("regionID" => $regionID)
        );
    }

    /**
     * @param mixed $fieldName
     */
    public function getAllByRegionName($fieldName)
    {
        return $this->collection->find(
            array("regionName" => $fieldName)
        );
    }

    /**
     * @param mixed $solarSystemID
     */
    public function getAllBySolarSystemID($solarSystemID)
    {
        return $this->collection->find(
            array("solarSystemID" => $solarSystemID)
        );
    }

    /**
     * @param mixed $fieldName
     */
    public function getAllBySolarSystemName($fieldName)
    {
        return $this->collection->find(
            array("solarSystemName" => $fieldName)
        );
    }

    /**
     * @param mixed $solarSystemNameID
     */
    public function getAllBySolarSystemNameID($solarSystemNameID)
    {
        return $this->collection->find(
            array("solarSystemNameID" => $solarSystemNameID)
        );
    }

    /**
     * @param mixed $starId
     */
    public function getAllByStarId($starId)
    {
        return $this->collection->find(
            array("star.id" => $starId)
        );
    }

    /**
     * @param mixed $starTypeID
     */
    public function getAllByStarTypeID($starTypeID)
    {
        return $this->collection->find(
            array("star.typeID" => $starTypeID)
        );
    }

    /**
     * @param mixed $sunTypeID
     */
    public function getAllBySunTypeID($sunTypeID)
    {
        return $this->collection->find(
            array("sunTypeID" => $sunTypeID)
        );
    }

    /**
     * @param array $documents An array of arrays. eg: array(array(data), array(data2), array(data3))
     * @param array $options Options array, used for projection, sort, etc.
     */
    public function insertMany(array $documents, array $options = [])
    {
        try {
            return $this->collection->insertMany($documents, $options);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param array $document The data array to insert.
     * @param array $options Options array, used for projection, sort, etc.
     */
    public function insertOne(array $document, array $options = [])
    {
        try {
            return $this->collection->insertOne($document, $options);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param array $filter A Filter query, eg: array("killID" => 1).
     * @param array $replacement The new data array to replace the old one with.
     * @param array $options Options array, used for projection, sort, etc.
     */
    public function updateOne(array $filter, array $replacement, array $options = [])
    {
        try {
            return $this->collection->replaceOne($filter, $replacement, $options);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
