<?php
namespace Thessia\Controller;

use Thessia\Middleware\Controller;

class IndexController extends Controller {
    public function index() {
        $this->render("index.html", array());
    }
}