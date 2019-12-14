<?php
use Slim\Http\Request;
use Slim\Http\Response;

use App\Controllers\Admin\PropertyController;
use App\Services\Properties;




// Api Routes
$app->group('/api',
    function () {

        $this->group('/administrator', function (){
            $this->delete('/properties', PropertyController::class.':delete')->setName('properties.delete');

            $this->post('/properties', PropertyController::class.':store')->setName('properties.store');
        });


        $this->get('', PropertyController::class. ':show');
    });


// Routes
$app->get('/',
    function (Request $request, Response $response, array $args) {
           return $response->withRedirect('/administrator/properties');
    });


// Admin routes
$app->group('/administrator',
    function () {
        $this->get('/properties', PropertyController::class. ':show')->setName('properties.show');

    });

