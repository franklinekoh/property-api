<?php
use Slim\Http\Request;
use Slim\Http\Response;

use App\Controllers\Admin\PropertyController;
use App\Services\Properties;




// Api Routes
$app->group('/api',
    function () {

        $this->get('', PropertyController::class. ':show');
    });


// Routes
$app->get('/',
    function (Request $request, Response $response, array $args) {
           return $response->withRedirect('/administrator/properties');
    });

$app->get('/properties/fetch',
    function (Request $request, Response $response, array $args) {

        $properties = new Properties($this->httpClient);
        $response = $properties->fetchAll(100, 2);

//        var_dump($response);

        var_dump(json_decode($response->getBody(), true));
        return;

        // Render index view
        return $this->renderer->render($response, 'index.phtml', $args);
    });

// Admin routes
$app->group('/administrator',
    function () {
        $this->get('/properties', PropertyController::class. ':show')->setName('properties.show');
    });

