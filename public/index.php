<?php
error_reporting(E_ALL);

if(PHP_SAPI == "cli-server") {
    $file = __DIR__ . $_SERVER["REQUEST_URI"];
    if(is_file($file))
        return false;
}

// Load the initialization file
include(__DIR__ . "/../init.php");

// Load slim
$app = new \Slim\App($container);

$app->run();