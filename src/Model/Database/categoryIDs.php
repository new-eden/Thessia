<?php
namespace Thessia\Model\Database;

use Thessia\Helper\Mongo;

/**
 */
class categoryIDs extends Mongo
{

    /**
     * The name of the models collection
     */
    public $collectionName = 'categoryIDs';

    /**
     * The name of the database the collection is stored in
     */
    public $databaseName = 'ccp';

    /**
     * An array of indexes for this collection
     */
    public $indexes = array();

    /**
     * @param mixed $categoryID
     */
    public function getAllByCategoryID($categoryID)
    {
        return $this->collection->find(
            array("categoryID" => $categoryID)
        );
    }

    /**
     * @param mixed $fieldName
     */
    public function getAllByName($fieldName)
    {
        return $this->collection->find(
            array("name.en" => $fieldName)
        );
    }

    public function getAllByGermanName($fieldName) {
        return $this->collection->find(
            array("name.de" => $fieldName)
        );
    }

    public function getAllByEnglishName($fieldName) {
        return $this->collection->find(
            array("name.en" => $fieldName)
        );
    }

    public function getAllByFrenchName($fieldName) {
        return $this->collection->find(
            array("name.fr" => $fieldName)
        );
    }

    public function getAllByJapaneseName($fieldName) {
        return $this->collection->find(
            array("name.ja" => $fieldName)
        );
    }

    public function getAllByRussianName($fieldName) {
        return $this->collection->find(
            array("name.ru" => $fieldName)
        );
    }

    public function getAllByChineseName($fieldName) {
        return $this->collection->find(
            array("name.zh" => $fieldName)
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
