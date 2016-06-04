<?php

// Keep at the top
$config = array();

// App config
$config["app"] = array(
    "url" => "http://thessia.karbowiak.dk",
    "hash" => array(
        "algorithm" => PASSWORD_BCRYPT,
        "cost" => 15
    )
);

// Slim / Twig settings
$config["settings"] = array(
    "debug" => true,
    "whoops.editor" => "sublime",
    "displayErrorDetails" => true,
    "view" => array(
        "cache" => __DIR__ . "../cache/",
        "debug" => true,
        "auto_reload" => true
    ),
    "name" => "Thessia",
    "path" => __DIR__ . "/../logs/thessia.log"
);

// Cache settings
$config["cache"] = array(
    "host" => "127.0.0.1",
    "port" => 6379
);

// Database settings
$config["db"] = array(
    "unixSocket" => "",
    "host" => "localhost",
    "port" => 3306,
    "username" => "",
    "password" => "",
    "emulatePrepares" => true,
    "usebufferedQuery" => true
);

return $config;