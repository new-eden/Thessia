<?php

namespace Thessia\Middleware;

use Slim\App;

/**
 * Class RenaBootstrap
 * @package Rena\Middleware
 */
class Bootstrap
{
    /**
     * @var App
     */
    protected $app;

    /**
     * RenaBootstrap constructor.
     */
    public function __construct() {
        require_once(__DIR__ . "/../../Init.php");
        $app = new App($container);
        require_once(__DIR__ . "/../../Config/Routes.php");

        $this->app = $app;
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function __invoke($request, $response) {
        $app = $this->app;
        return $app($request, $response);
    }
}