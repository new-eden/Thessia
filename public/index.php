<?php
error_reporting(1);
error_reporting(E_ALL);

if (PHP_SAPI == "cli-server") {
    $file = __DIR__ . $_SERVER["REQUEST_URI"];
    if (is_file($file))
        return false;
}

// Load the initialization file
include(__DIR__ . "/../init.php");

// Load slim
$app = new \Slim\App($container);

// Setup the session handler
$session = $container->get("session");
session_set_save_handler($session, true);
session_cache_limiter(false);
session_start();

// Setup whoops
$app->add(new \Thessia\Middleware\Whoops());

// Load the routes
require_once(__DIR__ . "/../config/routes.php");

// Start the application
$app->run();