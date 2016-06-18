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