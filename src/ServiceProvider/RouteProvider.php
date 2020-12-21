<?php

namespace XHGui\ServiceProvider;

use Exception;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use XHGui\Controller;
use XHGui\Twig\XHGuiTwigExtension;

class RouteProvider implements ServiceProviderInterface
{
    public function register(Container $di): void
    {
        $this->registerRoutes($di, $di['app']);
    }

    private function registerRoutes(Container $di, App $app): void
    {
        $app->error(static function (Exception $e) use ($di, $app): void {
            /** @var Twig $view */
            $view = $di['view'];
            $view->parserOptions['cache'] = false;
            $view->parserExtensions = [
                new XHGuiTwigExtension($app),
            ];

            $app->view($view);
            $app->render('error/view.twig', [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
        });

        // Profile Runs routes
        $app->get('/', static function (Request $request, Response $response) use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di['runController'];

            $controller->index($request, $response);
        })->setName('home');

        $app->get('/run/view', static function (Request $request, Response $response) use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di['runController'];

            $controller->view($request, $response);
        })->setName('run.view');

        $app->get('/run/delete', static function (Request $request) use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di['runController'];

            $controller->deleteForm($request);
        })->setName('run.delete.form');

        $app->post('/run/delete', static function (Request $request) use ($di): void {
            /** @var Controller\RunController $controller */
            $controller = $di['runController'];

            $controller->deleteSubmit($request);
        })->setName('run.delete.submit');

        $app->get('/run/delete_all', static function () use ($di): void {
            /** @var Controller\RunController $controller */
            $controller = $di['runController'];
            $controller->deleteAllForm();
        })->setName('run.deleteAll.form');

        $app->post('/run/delete_all', static function () use ($di): void {
            /** @var Controller\RunController $controller */
            $controller = $di['runController'];
            $controller->deleteAllSubmit();
        })->setName('run.deleteAll.submit');

        $app->get('/url/view', static function (Request $request) use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di['runController'];

            $controller->url($request);
        })->setName('url.view');

        $app->get('/run/compare', static function (Request $request) use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di['runController'];

            $controller->compare($request);
        })->setName('run.compare');

        $app->get('/run/symbol', static function (Request $request) use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di['runController'];

            $controller->symbol($request);
        })->setName('run.symbol');

        $app->get('/run/symbol/short', static function (Request $request) use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di['runController'];

            $controller->symbolShort($request);
        })->setName('run.symbol-short');

        $app->get('/run/callgraph', static function (Request $request) use ($di, $app): void {
            /** @var Controller\RunController $controller */
            $controller = $di['runController'];

            $controller->callgraph($request);
        })->setName('run.callgraph');

        $app->get('/run/callgraph/data', static function (Request $request, Response $response) use ($di): void {
            /** @var Controller\RunController $controller */
            $controller = $di['runController'];

            $controller->callgraphData($request, $response);
        })->setName('run.callgraph.data');

        $app->get('/run/callgraph/dot', static function (Request $request, Response $response) use ($di): void {
            /** @var Controller\RunController $controller */
            $controller = $di['runController'];

            $controller->callgraphDataDot($request, $response);
        })->setName('run.callgraph.dot');

        // Import route
        $app->post('/run/import', static function (Request $request, Response $response) use ($di): void {
            /** @var Controller\ImportController $controller */
            $controller = $di['importController'];

            $controller->import($request, $response);
        })->setName('run.import');

        // Watch function routes.
        $app->get('/watch', static function () use ($di): void {
            /** @var Controller\WatchController $controller */
            $controller = $di['watchController'];
            $controller->get();
        })->setName('watch.list');

        $app->post('/watch', static function (Request $request) use ($di): void {
            /** @var Controller\WatchController $controller */
            $controller = $di['watchController'];

            $controller->post($request);
        })->setName('watch.save');

        // Custom report routes.
        $app->get('/custom', static function () use ($di): void {
            /** @var Controller\CustomController $controller */
            $controller = $di['customController'];
            $controller->get();
        })->setName('custom.view');

        $app->get('/custom/help', static function (Request $request) use ($di, $app): void {
            /** @var Controller\CustomController $controller */
            $controller = $di['customController'];

            $controller->help($request);
        })->setName('custom.help');

        $app->post('/custom/query', static function (Request $request, Response $response) use ($di): void {
            /** @var Controller\CustomController $controller */
            $controller = $di['customController'];

            $controller->query($request, $response);
        })->setName('custom.query');

        // Waterfall routes
        $app->get('/waterfall', static function () use ($di): void {
            /** @var Controller\WaterfallController $controller */
            $controller = $di['waterfallController'];
            $controller->index();
        })->setName('waterfall.list');

        $app->get('/waterfall/data', static function (Request $request, Response $response) use ($di): void {
            /** @var Controller\WaterfallController $controller */
            $controller = $di['waterfallController'];

            $controller->query($request, $response);
        })->setName('waterfall.data');

        // Metrics
        $app->get('/metrics', static function (Request $request, Response $response) use ($di): void {
            /** @var Controller\MetricsController $controller */
            $controller = $di['metricsController'];

            $controller->metrics($response);
        })->setName('metrics');
    }
}
