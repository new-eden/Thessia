<?php

$app->group("", function () use ($app) {
    $controller = new \Thessia\Controller\IndexController($app);
    $app->get("/", $controller("index"));
});