<?php

namespace Thessia\Middleware;

use Psr\Http\Message\ServerRequestInterface;

class Request extends \Slim\Http\Request implements ServerRequestInterface {
    public function getCookie($name, $defaultValue = null) {
        $cookies = $this->getCookieParams();
        return array_key_exists($name, $cookies) ? $cookies[$name] : $defaultValue;
    }
}