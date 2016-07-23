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

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whoops\Run as WhoopsRun;

class WhoopsErrorHandler
{
    private $whoops;

    public function __construct(WhoopsRun $whoops)
    {
        $this->whoops = $whoops;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, Exception $exception)
    {
        $handler = WhoopsRun::EXCEPTION_HANDLER;
        ob_start();
        $this->whoops->$handler($exception);
        $content = ob_get_clean();
        $code = $exception instanceof HttpException ? $exception->getStatusCode() : 500;
        return $response
            ->withStatus($code)
            ->withHeader('Content-type', 'text/html')
            ->write($content);
    }

    private function renderException(Exception $exception)
    {
    }
}