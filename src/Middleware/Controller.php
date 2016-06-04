<?php
namespace Thessia\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;

/**
 * Class RenaController
 * @package Thessia\Middleware
 */
abstract class RenaController
{
    // Optional properties
    /**
     * @var App
     */
    protected $app;
    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * @var ResponseInterface
     */
    protected $response;
    /**
     * @var \Interop\Container\ContainerInterface
     */
    private $container;

    /**
     * RenaController constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->container = $app->getContainer();
    }

    /**
     * @param $actionName
     * @return \Closure
     */
    public function __invoke($actionName)
    {
        $app = $this->app;
        $controller = $this;
        $callable = function ($request, $response, $args) use ($app, $controller, $actionName) {
            if (method_exists($controller, 'setRequest')) {
                $controller->setRequest($request);
            }

            if (method_exists($controller, 'setResponse')) {
                $controller->setResponse($response);
            }

            if (method_exists($controller, 'init')) {
                $controller->init();
            }

            // store the name of the controller and action so we can assert during tests
            $controllerName = get_class($controller);
            $controllerName = strtolower($controllerName);
            $controllerNameParts = explode('\\', $controllerName);
            $controllerName = array_pop($controllerNameParts);
            preg_match('/(.*)controller$/', $controllerName, $result);
            $controllerName = $result[1];

            // these values will be useful when testing, but not included with the
            // Slim\Http\Response. Instead use SlimMvc\Http\Response
            if (method_exists($response, 'setControllerName')) {
                $response->setControllerName($controllerName);
            }

            if (method_exists($response, 'setActionName')) {
                $response->setActionName($actionName);
            }

            return call_user_func_array(array($controller, $actionName), $args);
        };

        return $callable;
    }


    /**
     * @param $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @param $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @param String $file
     * @param array $args
     * @param int $status
     * @param String $contentType
     * @return mixed
     */
    protected function render(String $file, $args = array(), int $status = 200, String $contentType = "text/html; charset=UTF-8") {
        // Render the view using the render method
        return $this->container->render->render($file, $args, $status, $contentType, $this->response);
    }

    // @TODO add a fourth that is just called api, which figures out what the user has told you they want (xml / json) and use that as the header)
    // Remember to create a new response, with the new header that the user has requested in $this->requested
    // Something like: $response = $this->response->withHeader($this->requested->getHeader("Content-Type")); and then pass on $response
    /**
     * @param array $args
     * @param int $status
     * @return mixed
     */
    protected function json($args = array(), int $status = 200) {
        return $this->container->render->toJson($args, $status, $this->response);
    }

    /**
     * @param array $args
     * @param int $status
     * @return mixed
     */
    protected function xml($args = array(), int $status = 200) {
        return $this->container->render->toXML($args, $status, $this->response);
    }

    /**
     * @return mixed
     */
    protected function isXhr()
    {
        return $this->request->isXhr();
    }

    /**
     * @return array
     */
    protected function getPost()
    {
        $post = array_diff_key($this->request->getParams(), array_flip(array(
            '_METHOD',
        )));
        return $post;
    }

    /**
     * @return mixed
     */
    protected function getQueryParams()
    {
        return $this->request->getQueryParams();
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function get($name)
    {
        return $this->app->getContainer()->get($name);
    }

    /**
     * @param $url
     * @param int $status
     * @return mixed
     */
    protected function redirect($url, $status = 302)
    {
        return $this->response->withRedirect($url, $status);
    }

    /**
     * @param $actionName
     * @param array $data
     * @return mixed
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