<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2016. Michael Karbowiak
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Thessia\Middleware;

use PHPPM\Bridges\BridgeInterface;
use PHPPM\React\HttpResponse as ReactResponse;
use Psr\Http\Message\ResponseInterface as Psr7Response;
use React\Http\Request as ReactRequest;
use Zend\Diactoros\Response as DiactorosResponse;
use Zend\Diactoros\ServerRequest as DiactorosRequest;
use Zend\Diactoros\Stream as DiactorosStream;

/**
 * Class RenaBridge
 * @package Rena\Lib\Middleware
 */
class Bridge implements BridgeInterface
{
    /**
     * @var
     */
    protected $middleware;


    /**
     * @param string $appBootstrap
     * @param $appenv
     * @param bool $debug
     */
    public function bootstrap($appBootstrap, $appenv, $debug)
    {
        $this->middleware = new $appBootstrap;
    }

    /**
     * @return string
     */
    public function getStaticDirectory()
    {
        return "./public/";
    }

    /**
     * @param ReactRequest $request
     * @param ReactResponse $response
     */
    public function onRequest(ReactRequest $request, ReactResponse $response)
    {
        if ($this->middleware === null) {
            return;
        }

        $renaReq = $this->mapRequest($request);

        $middleware = $this->middleware;

        $initialResponse = new DiactorosResponse;
        $renaResp = $middleware($renaReq, $initialResponse);

        $this->mapResponse($renaResp, $response);
    }

    /**
     * Convert a ReactPHP request into a PHP-7 compatible one.
     *
     * @param ReactRequest $request
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function mapRequest(ReactRequest $request)
    {
        return new DiactorosRequest(
            $_SERVER,
            $request->getFiles(),
            $request->getUrl(),
            $request->getMethod(),
            $this->getBodyStreamFrom($request),
            $request->getHeaders(),
            $this->getCookiesFrom($request),
            $request->getQuery(),
            $request->getPost(),
            $request->getHttpVersion()
        );
    }

    /**
     * Load the response information onto the given ReactPHP response object.
     *
     * @param Psr7Response $psr7Response
     * @param ReactResponse $response
     */
    private function mapResponse(Psr7Response $psr7Response, ReactResponse $response)
    {
        if (PHP_SESSION_ACTIVE === session_status()) {
            session_write_close();
            session_unset();
        }

        $response->writeHead(
            $psr7Response->getStatusCode(),
            $psr7Response->getHeaders()
        );

        $response->end(
            $psr7Response->getBody()
        );
    }

    /**
     * @param ReactRequest $request
     * @return \Psr\Http\Message\StreamInterface
     */
    private function getBodyStreamFrom(ReactRequest $request)
    {
        $bodyStream = new DiactorosStream('php://temp', 'rw');
        $bodyStream->write($request->getBody());

        return $bodyStream;
    }

    /**
     * @param ReactRequest $request
     * @return array
     */
    private function getCookiesFrom(ReactRequest $request)
    {
        $headers = $request->getHeaders();

        if (!isset($headers['Cookie']) && !isset($headers['cookie'])) {
            return [];
        }

        $cookieHeader = explode(';', isset($headers['Cookie']) ? $headers['Cookie'] : $headers['cookie']);

        return array_reduce($cookieHeader, function($cookies, $cookie) {
            list($name, $value) = explode('=', trim($cookie));
            $cookies[$name] = $value;
            return $cookies;
        }, []);
    }
}