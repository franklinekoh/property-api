<?php
use Slim\Http\Request;
use Slim\Http\Response;

use App\Controllers\ExampleController;


// Api Routes
$app->group('/api',
    function () {

        $this->get('', ExampleController::class. ':show');
    });


// Routes
$app->get('/[{name}]',
    function (Request $request, Response $response, array $args) {
        // Sample log message
        $this->logger->info("Slim-Skeleton '/' route");

        // Render index view
        return $this->renderer->render($response, 'index.phtml', $args);
    });
