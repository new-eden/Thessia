<?php
namespace Thessia\Controller;

use Thessia\Lib\Middleware\Controller;

class IndexController extends Controller {
    public function index() {
        $this->render("index.html", array());
    }
}