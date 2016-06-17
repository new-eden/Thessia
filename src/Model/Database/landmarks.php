<?php
namespace Thessia\Model\Database;

use Thessia\Helper\Mongo;

/**
 */
class landmarks extends Mongo
{

    /**
     * The name of the models collection
     */
    public $collectionName = 'landmarks';

    /**
     * The name of the database the collection is stored in
     */
    public $databaseName = 'ccp';

    /**
     * An array of indexes for this collection
     */
    public $indexes = array();

    /**
     * @param mixed $descriptionID
     */
    public function getAllByDescriptionID($descriptionID)
    {
        return $this->collection->find(
            array("descriptionID" => $descriptionID)
        );
    }

    /**
     * @param mixed $landmarkID
     */
    public function getAllByLandmarkID($landmarkID)
    {
        return $this->collection->find(
            array("landmarkID" => $landmarkID)
        );
    }

    /**
     * @param mixed $landmarkNameID
     */
    public function getAllByLandmarkNameID($landmarkNameID)
    {
        return $this->collection->find(
            array("landmarkNameID" => $landmarkNameID)
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