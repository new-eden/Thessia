<?php

$app->group("/character", function() use ($app) {
    $controller = new \Thessia\Controller\CharacterController($app);
    $app->get("/[{characterID:[0-9]+}/]", $controller("index"));
});