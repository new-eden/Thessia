<?php

namespace Thessia\Middleware;

use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Thessia\Lib\Render;

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
    public function __construct()
    {
        require_once(__DIR__ . "/../../init.php");

        // Load the slim dependency
        $container->addServiceProvider(new \Jenssegers\Lean\SlimServiceProvider);

        // Add the twig view
        $container->share("view", Twig::class)->withArguments(array(__DIR__ . "/../../templates", $container->get("config")->getAll("settings")["view"]));
        $container->get("view")->addExtension(new TwigExtension($container->get("router"), $container->get("request")->getUri()));
        $container->get("view")->addExtension(new \Twig_Extension_Debug());

        // Add the Renderer
        $container->share("render", Render::class)->withArgument("view");

        $app = new App($container);
        require_once(__DIR__ . "/../../config/routes.php");

        $this->app = $app;
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function __invoke($request, $response)
    {
        $app = $this->app;
        return $app($request, $response);
    }
}