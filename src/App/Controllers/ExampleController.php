<?php


namespace App\Controllers;

use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;
use Psr\Container\ContainerInterface;

class ExampleController
{

    /** @var \League\Fractal\Manager */
    protected $fractal;
    /** @var \Illuminate\Database\Capsule\Manager */
    protected $db;
    /** @var \Conduit\Validation\Validator */
    protected $validator;

    /**
     * UserController constructor.
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @internal param $auth
     */
    public function __construct(ContainerInterface $container)
    {
        $this->fractal = $container->get('fractal');
        $this->validator = $container->get('validator');
        $this->db = $container->get('db');
    }


    public function show(Request $request, Response $response)
    {


            return $response->withJson(['working']);
    }

}