<?php
namespace Thessia\Controller;

use Thessia\Lib\Middleware\Controller;

class IndexController extends Controller{
    public function index() {
        $container = $this->app->getContainer();
        var_dump($container->get("config"));
        $this->render("index.html", array());
    }
}