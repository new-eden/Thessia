<?php

$app->group("/character", function() use ($app) {
    $controller = new \Thessia\Controller\CharacterController($app);
    $app->group("/{characterID:[0-9]+}", function() use ($app, $controller) {
        $app->get("/", $controller("index"));
        $app->get("/history/", $controller("history"));
    });
});