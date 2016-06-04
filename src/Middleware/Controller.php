<?php

namespace Thessia\Lib\Middleware;

use Psr\Http\Message\UriInterface;

abstract class Controller
{
    // Optional properties
    protected $app;
    protected $request;
    protected $response;

    /**
     * @param \Slim\App $app
     */
    public function __construct(\Slim\App $app)
    {
        $this->app = $app;
    }

    // Optional setters

    // public function setApp($app)
    // {
    //     $this->app = $app;
    // }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * Render the view from within the controller
     * @param string $file Name of the template/ view to render
     * @param array $args Additional variables to pass to the view
     * @param Response?
     * TODO should this be here?
     */
    protected function render($file, $args=array())
    {
        //$container = $this->app->getContainer();
        //var_dump($container->get("view")); die();

        // return $container->renderer->render($this->response, $file, $args);
        //return $container->get("render")->render($this->response, $file, $args);
    }

    /**
     * Return true if XHR request
     */
    protected function isXhr()
    {
        return $this->request->isXhr();
    }

    /**
     * Get the POST params
     */
    protected function getPost()
    {
        $post = array_diff_key($this->request->getParams(), array_flip(array(
            '_METHOD',
        )));

        return $post;
    }

    /**
     * Get the POST params
     */
    protected function getQueryParams()
    {
        return $this->request->getQueryParams();
    }

    /**
     * Shorthand method to get dependency from container
     * @param $name
     * @return mixed
     */
    protected function get($name)
    {
        return $this->app->getContainer()->get($name);
    }

    /**
     * Redirect.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * This method prepares the response object to return an HTTP Redirect
     * response to the client.
     *
     * @param  string|UriInterface $url    The redirect destination.
     * @param  int                 $status The redirect HTTP status code.
     * @return self
     */
    protected function redirect($url, $status = 302)
    {
        return $this->response->withRedirect($url, $status);
    }

    /**
     * Pass on the control to another action. Of the same class (for now)
     *
     * @param  string $actionName The redirect destination.
     * @param array $data
     * @return Controller
     * @internal param string $status The redirect HTTP status code.
     */
    public function forward($actionName, $data=array())
    {
        // update the action name that was last used
        if (method_exists($this->response, 'setActionName')) {
            $this->response->setActionName($actionName);
        }

        return call_user_func_array(array($this, $actionName), $data);
    }
}