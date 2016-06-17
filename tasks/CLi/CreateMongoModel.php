<?php

namespace Thessia\Tasks\CLi;


use gossi\codegen\generator\CodeFileGenerator;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpMethod;
use gossi\codegen\model\PhpParameter;
use gossi\codegen\model\PhpProperty;
use MongoDB\Client;
use MongoDB\Model\BSONDocument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMongoModel extends Command {
    private $description = "";
    private $idFields = array();
    private $nameFields = array();

    protected function configure() {
        $this
            ->setName("create:mongo")
            ->setDescription("Creates a model from a database collection");
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        $collectionName = prompt("Collection to create a model for");
        $this->description = prompt("Description");

        if(!$collectionName)
            return $output->writeln("Error, collection name is not defined");

        $path = __DIR__ . "/../../src/Model/Database/{$collectionName}.php";
        if(file_exists($path))
            return $output->writeln("Error, model already exists");

        // Get the container
        $container = getContainer();

        // Get Mongo
        /** @var Client $mongo */
        $mongo = $container->get("mongo");

        // Select the collection
        try {
            $collection = $mongo->selectCollection($container->get("config")->get("dbName", "mongodb"), $collectionName);
        } catch(\Exception $e) {
            return $output->writeln("Error, collection doesn't exist");
        }

        // Get the first result from the collection (We just assume that the first result is always a proper example of the format in the collection)
        /** @var BSONDocument $data */
        $data = $collection->findOne(
            array(),
            array("projection" =>
                array(
                    "_id" => 0
                )
            )
        )->getArrayCopy();

        // Generate the start of the code
        $class = new PhpClass();
        $class->setQualifiedName("Thessia\\Model\\Database\\{$collectionName} extends Mongo")
            ->setProperty(PhpProperty::create("collectionName")
                ->setVisibility("public")
                ->setValue($collectionName)
                ->setDescription("The name of the models collection"))
            ->setProperty(PhpProperty::create("databaseName")
                ->setVisibility("public")
                ->setValue($container->get("config")->get("dbName", "mongodb"))
                ->setDescription("The name of the database the collection is stored in"))
            ->setProperty(PhpProperty::create("indexes")
                ->setVisibility("public")
                ->setValue(array())
                ->setDescription("An array of indexes for this collection"))
            ->setDescription($this->description)
            ->declareUse("Thessia\\Helper\\Mongo");

        // Populate all the fields to be created
        $this->populateFields($data);
        $this->populateFields($data["victim"], "victim");
        $this->populateFields($data["attackers"][0], "attacker");
        $this->populateFields($data["items"][0], "item");

        // Get Generators
        foreach($this->nameFields as $functionName => $fieldName) {
            $class->setMethod(PhpMethod::create("getAllBy" . ucfirst($functionName))
                ->addParameter(PhpParameter::create("fieldName"))
                ->setVisibility("public")
                ->setBody("return \$this->collection->find(
                    array(\"{$fieldName}\" => \$fieldName)
                );"));
        }

        // Get Generators for ID fields
        foreach($this->idFields as $functionName => $fieldName) {
            $class->setMethod(PhpMethod::create("getAllBy" . ucfirst($functionName))
                ->addParameter(PhpParameter::create($functionName))
                ->setReferenceReturned(true)
                ->setVisibility("public")
                ->setBody("return \$this->collection->find(
                    array(\"{$fieldName}\" => \${$functionName})
                );"));
        }

        // Get Generators for selecting individual fields
        // Do i even need these? Oh well, they're here so i can enable them if i need to..
        //$this->getIndividualFields($class, $data);
        //$this->getIndividualFields($class, $data["victim"], "victim");
        //$this->getIndividualFields($class, $data["attackers"][0], "attacker");
        //$this->getIndividualFields($class, $data["items"][0], "item");

        // The Update Function (Just uses ReplaceOne)
        $class->setMethod(PhpMethod::create("updateOne")
            ->addParameter(PhpParameter::create("filter")
                ->setType("array")
                ->setDescription("A Filter query, eg: array(\"killID\" => 1).")
            )
            ->addParameter(PhpParameter::create("replacement")
                ->setType("array")
                ->setDescription("The new data array to replace the old one with.")
            )
            ->addParameter(PhpParameter::create("options")
                ->setType("array")
                ->setValue(array())
                ->setDescription("Options array, used for projection, sort, etc.")
            )
            ->setVisibility("public")
            ->setBody("try {
                return \$this->collection->replaceOne(\$filter, \$replacement, \$options);
                } catch (\\Exception \$e) {
                    return \$e->getMessage();
                }"));

        // Insert function
        $class->setMethod(PhpMethod::create("insertOne")
            ->addParameter(PhpParameter::create("document")
                ->setType("array")
                ->setDescription("The data array to insert.")
            )
            ->addParameter(PhpParameter::create("options")
                ->setType("array")
                ->setValue(array())
                ->setDescription("Options array, used for projection, sort, etc.")
            )
            ->setVisibility("public")
            ->setBody("try {
                return \$this->collection->insertOne(\$document, \$options);
                } catch (\\Exception \$e) {
                    return \$e->getMessage();
                }"));

        // Insert many function
        $class->setMethod(PhpMethod::create("insertMany")
            ->addParameter(PhpParameter::create("documents")
                ->setType("array")
                ->setDescription("An array of arrays. eg: array(array(data), array(data2), array(data3))")
            )
            ->addParameter(PhpParameter::create("options")
                ->setType("array")
                ->setValue(array())
                ->setDescription("Options array, used for projection, sort, etc.")
            )
            ->setVisibility("public")
            ->setBody("try {
                return \$this->collection->insertMany(\$documents, \$options);
                } catch (\\Exception \$e) {
                    return \$e->getMessage();
                }"));

        $generator = new CodeFileGenerator(array(
            "headerComment" => $this->description
        ));
        $code = $generator->generate($class);

        // Create the model
        file_put_contents($path, $code);
        chmod($path, 0777);

        $output->write("Model created: {$path}");
    }

    private function getIndividualFields(PhpClass $phpClass, $array, $type = null) {
        foreach($this->nameFields as $functionName => $fieldName) {
            if(stristr($functionName, $type)) {
                $name = lcfirst(str_replace($type, "", $functionName));
                foreach ($array as $key => $value) {
                    if ($key == $name || $key == "_id")
                        continue;

                    $phpClass->setMethod(PhpMethod::create("get" . ucfirst($key) . "By" . ucfirst($functionName))
                        ->addParameter(PhpParameter::create($name))
                        ->setVisibility("public")
                        ->setBody("return \$this->collection->find(
                        array(\"{$fieldName}\" => \${$name}),
                        array(\"projection\" => array(\"{$key}\" => 1)
                    );"));
                }
            }
        }

        foreach($this->idFields as $functionName => $fieldName) {
            if (stristr($functionName, $type)) {
                $name = lcfirst(str_replace($type, "", $functionName));
                foreach ($array as $key => $value) {
                    if ($key == $name || $key == "_id")
                        continue;

                    $phpClass->setMethod(PhpMethod::create("get" . ucfirst($key) . "By" . ucfirst($functionName))
                        ->addParameter(PhpParameter::create($name))
                        ->setVisibility("public")
                        ->setBody("return \$this->collection->find(
                        array(\"{$fieldName}\" => \${$name}),
                        array(\"projection\" => array(\"{$key}\" => 1)
                    );"));
                }
            }
        }
    }

    private function populateFields($array, $type = null) {
        foreach($array as $key => $value) {
            if(stristr($key, "name")) {
                if(isset($type) && !is_numeric($type))
                    $this->nameFields[$type . ucfirst($key)] = $type . "." . $key;
                else
                    $this->nameFields[$key] = $key;
            }
            if(stristr($key, "ID")) {
                if(isset($type) && !is_numeric($type))
                    $this->idFields[$type . ucfirst($key)] = $type . "." . $key;
                else
                    $this->idFields[$key] = $key;
            }
            if(stristr($key, "Hash")) {
                if(isset($type) && !is_numeric($type))
                    $this->idFields[$type . ucfirst($key)] = $type . "." . $key;
                else
                    $this->idFields[$key] = $key;
            }
        }
    }

}