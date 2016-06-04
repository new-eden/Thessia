<?php
namespace Thessia\Controller;

use Thessia\Lib\Middleware\Controller;

class IndexController extends Controller{
    public function index() {
        var_dump($this->app->getContainer()); die();
        $this->render("index.html", array());
    }
}