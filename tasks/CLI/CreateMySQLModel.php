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

namespace Thessia\Tasks\CLI;

use gossi\codegen\generator\CodeFileGenerator;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpMethod;
use gossi\codegen\model\PhpParameter;
use gossi\codegen\model\PhpProperty;
use gossi\formatter\Formatter;
use Rena\Lib\Db;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMySQLModel extends Command
{
    protected function configure()
    {
        $this
            ->setName("create:mysql")
            ->setDescription("Create a model from a database table");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = prompt("Table to create model for");
        $description = prompt("Description of the model");

        if (!$table) {
            $output->writeln("Error, table name not supplied");
            exit();
        }

        $path = __DIR__ . "/../src/Model/CCP/{$table}.php";
        if (file_exists($path)) {
            return $output->writeln("Error, model already exists");
        }

        // Load the datababase
        global $container; // Urgh, globals
        /** @var Db $db */
        $db = $container["db"];

        $columns = $db->query("SHOW COLUMNS FROM {$table}");

        $class = new PhpClass();
        $class->setQualifiedName("Rena\\Models\\CCP\\{$table}")
            ->setProperty(PhpProperty::create("db")
                ->setVisibility("private")
                ->setDocblock("/** @var \\Rena\\Lib\\Db \$db */")
                ->setDescription("CCP Connection")
            )
            ->setMethod(PhpMethod::create("__construct")
                ->addParameter(PhpParameter::create("db")
                    ->setType("\\Rena\\Lib\\Db"))
                ->setBody("\$this->db = \$db;"))
            ->setDescription($description)
            ->declareUse("Rena\\Lib");

        $nameFields = array();
        $idFields = array();
        foreach ($columns as $get) {
            // This is for the getByName selector(s)
            if (stristr($get["Field"], "name")) {
                $nameFields[] = $get["Field"];
            }

            // This is for the getByID selector(s)
            if (strstr($get["Field"], "ID")) {
                $idFields[] = $get["Field"];
            }

            // This is for the getByHash selector(s)
            if (stristr($get["Field"], "Hash")) {
                $idFields[] = $get["Field"];
            }
        }

        // Get generator
        foreach ($nameFields as $name) {
            // get * by Name
            $class->setMethod(PhpMethod::create("getAllBy" . ucfirst($name))
                ->addParameter(PhpParameter::create($name))
                ->setVisibility("public")
                ->setBody("return \$this->db->query(\"SELECT * FROM {$table} WHERE {$name} = :{$name}\", array(\":{$name}\" => \${$name}));")
            );
        }

        foreach ($idFields as $id) {
            // get * by ID,
            $class->setMethod(PhpMethod::create("getAllBy" . ucfirst($id))
                ->addParameter(PhpParameter::create($id)
                    ->setType("int"))
                ->setVisibility("public")
                ->setBody("return \$this->db->query(\"SELECT * FROM {$table} WHERE {$id} = :{$id}\", array(\":{$id}\" => \${$id}));")
            );
        }

        foreach ($nameFields as $name) {
            foreach ($columns as $get) {
                // If the fields match, skip it.. no reason to get/set allianceID where allianceID = allianceID
                if ($get["Field"] == $name) {
                    continue;
                }

                // Skip the id field
                if ($get["Field"] == "id") {
                    continue;
                }

                $class->setMethod(PhpMethod::create("get" . ucfirst($get["Field"]) . "By" . ucfirst($name))
                    ->addParameter(PhpParameter::create($name))
                    ->setVisibility("public")
                    ->setBody("return \$this->db->queryField(\"SELECT {$get["Field"]} FROM {$table} WHERE {$name} = :{$name}\", \"{$get["Field"]}\", array(\":{$name}\" => \${$name}));")
                );
            }
        }

        foreach ($idFields as $id) {
            foreach ($columns as $get) {
                // If the fields match, skip it.. no reason to get/set allianceID where allianceID = allianceID
                if ($get["Field"] == $id) {
                    continue;
                }

                // Skip the id field
                if ($get["Field"] == "id") {
                    continue;
                }

                $class->setMethod(PhpMethod::create("get" . ucfirst($get["Field"]) . "By" . ucfirst($id))
                    ->addParameter(PhpParameter::create($id))
                    ->setVisibility("public")
                    ->setBody("return \$this->db->queryField(\"SELECT {$get["Field"]} FROM {$table} WHERE {$id} = :{$id}\", \"{$get["Field"]}\", array(\":{$id}\" => \${$id}));")
                );
            }
        }

        // Update generator
        foreach ($nameFields as $name) {
            foreach ($columns as $get) {
                // If the fields match, skip it.. no reason to get/set allianceID where allianceID = allianceID
                if ($get["Field"] == $name) {
                    continue;
                }

                // Skip the id field
                if ($get["Field"] == "id") {
                    continue;
                }

                $class->setMethod(PhpMethod::create("update" . ucfirst($get["Field"]) . "By" . ucfirst($name))
                    ->addParameter(PhpParameter::create($get["Field"]))
                    ->addParameter(PhpParameter::create($name))
                    ->setVisibility("public")
                    ->setBody("\$exists = \$this->db->queryField(\"SELECT {$get["Field"]} FROM {$table} WHERE {$name} = :{$name}\", \"{$get["Field"]}\", array(\":{$name}\" => \${$name}));
                     if(!empty(\$exists)){
                        \$this->db->execute(\"UPDATE {$table} SET {$get["Field"]} = :{$get["Field"]} WHERE {$name} = :{$name}\", array(\":{$name}\" => \${$name}, \":{$get["Field"]}\" => \${$get["Field"]}));}
                    ")
                );
            }
        }

        foreach ($idFields as $id) {
            foreach ($columns as $get) {
                // If the fields match, skip it.. no reason to get/set allianceID where allianceID = allianceID
                if ($get["Field"] == $id) {
                    continue;
                }

                // Skip the id field
                if ($get["Field"] == "id") {
                    continue;
                }

                $class->setMethod(PhpMethod::create("update" . ucfirst($get["Field"]) . "By" . ucfirst($id))
                    ->addParameter(PhpParameter::create($get["Field"]))
                    ->addParameter(PhpParameter::create($id))
                    ->setVisibility("public")
                    ->setBody("\$exists = \$this->db->queryField(\"SELECT {$get["Field"]} FROM {$table} WHERE {$id} = :{$id}\", \"{$get["Field"]}\", array(\":{$id}\" => \${$id}));
                     if(!empty(\$exists))
                     {
                        \$this->db->execute(\"UPDATE {$table} SET {$get["Field"]} = :{$get["Field"]} WHERE {$id} = :{$id}\", array(\":{$id}\" => \${$id}, \":{$get["Field"]}\" => \${$get["Field"]}));}
                    ")
                );
            }
        }

        // Global insert generator (Yes it's ugly as shit..)
        $inserter = "public function insertInto" . ucfirst($table) . "(";
        foreach ($columns as $field) {
            // Skip the ID field
            if ($field["Field"] == "id") {
                continue;
            }

            $inserter .= "\${$field["Field"]}, ";
        }
        $inserter = rtrim(trim($inserter), ",") . ")";
        $inserter .= "{";
        $inserter .= "\$this->db->execute(\"INSERT INTO {$table} (";
        foreach ($columns as $field) {
            if ($field["Field"] == "id") {
                continue;
            }

            $inserter .= $field["Field"] . ", ";
        }

        $inserter = rtrim(trim($inserter), ",") . ") ";
        $inserter .= "VALUES (";
        foreach ($columns as $field) {
            if ($field["Field"] == "id") {
                continue;
            }

            $inserter .= ":" . $field["Field"] . ", ";
        }

        $inserter = rtrim(trim($inserter), ",") . ")\", ";

        $inserter .= "array(";
        foreach ($columns as $field) {
            if ($field["Field"] == "id") {
                continue;
            }

            $inserter .= "\":" . $field["Field"] . "\" => \${$field["Field"]}, ";
        }

        $inserter = rtrim(trim($inserter), ",") . "));";
        $inserter .= "}}";

        $generator = new CodeFileGenerator();
        $code = $generator->generate($class);

        $code = rtrim(trim($code), "}");
        $code .= $inserter;
        $formatter = new Formatter();
        $code = $formatter->format($code);

        file_put_contents($path, $code);
        chmod($path, 0777);
        $output->writeln("Model created: {$path}");
    }
}