<?php
namespace Thessia\Middleware;

use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Util\Misc;

class Whoops
{

    public function __invoke($request, $response, $next)
    {
        $app = $next;
        $container = $app->getContainer();
        $settings = $container->get('settings');
        $environment = $container->get('environment');

        if (isset($settings['debug']) === true && $settings['debug'] === true) {
            // Enable PrettyPageHandler with editor options
            $prettyPageHandler = new PrettyPageHandler();

            if (empty($settings['whoops.editor']) === false) {
                $prettyPageHandler->setEditor($settings['whoops.editor']);
            }

            // Add more information to the PrettyPageHandler
            $prettyPageHandler->addDataTable('Slim Application', [
                'Application Class' => get_class($app),
                'Script Name' => $environment->get('SCRIPT_NAME'),
                'Request URI' => $environment->get('PATH_INFO') ?: '<none>',
            ]);

            $prettyPageHandler->addDataTable('Slim Application (Request)', array(
                'Accept Charset' => $request->getHeader('ACCEPT_CHARSET') ?: '<none>',
                'Content Charset' => $request->getContentCharset() ?: '<none>',
                'Path' => $request->getUri()->getPath(),
                'Query String' => $request->getUri()->getQuery() ?: '<none>',
                'HTTP Method' => $request->getMethod(),
                'Base URL' => (string)$request->getUri(),
                'Scheme' => $request->getUri()->getScheme(),
                'Port' => $request->getUri()->getPort(),
                'Host' => $request->getUri()->getHost(),
            ));

            // Set Whoops to default exception handler
            $whoops = new \Whoops\Run;
            $whoops->pushHandler($prettyPageHandler);

            // Enable JsonResponseHandler when request is AJAX
            if (Misc::isAjaxRequest()) {
                $whoops->pushHandler(new JsonResponseHandler());
            }

            $whoops->register();

            $container->share("errorHandler", function () use ($whoops) {
                return new WhoopsErrorHandler($whoops);
            });

            $container->share("whoops", function () use ($whoops) {
                return $whoops;
            });
        }

        return $app($request, $response);
    }

}
