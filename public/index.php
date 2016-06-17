<?php
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Thessia\Lib\Render;

error_reporting(1);
error_reporting(E_ALL);

if (PHP_SAPI == "cli-server") {
    $file = __DIR__ . $_SERVER["REQUEST_URI"];
    if (is_file($file))
        return false;
}

// Load the initialization file
include(__DIR__ . "/../init.php");

// Load the slim dependency
$container->addServiceProvider(new \Jenssegers\Lean\SlimServiceProvider);

// Add the twig view
$container->share("view", Twig::class)->withArguments(array(__DIR__ . "/../templates", $container->get("config")->getAll("settings")["view"]));
$container->get("view")->addExtension(new TwigExtension($container->get("router"), $container->get("request")->getUri()));
$container->get("view")->addExtension(new \Twig_Extension_Debug());

// Add the Renderer
$container->share("render", Render::class)->withArgument("view");

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