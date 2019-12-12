<?php

namespace App\Controllers\Admin;

use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;
use Psr\Container\ContainerInterface;
use App\Models\Property;


class PropertyController
{

    /**
     *Validator for input requests
     * @var
     */
    protected $validator;

    /**
     * Rendering template (php-view)
     * @var
     */
    protected $renderer;

    /**
     * PropertyController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->validator = $container->get('validator');
        $this->renderer = $container->get('renderer');
    }

    public function show(Request $request, Response $response){

        $data = Property::with(['propertyType'])
                    ->orderBy('created_at', 'DESC')
                        ->get();

        $this->renderer->setLayout('layout.phtml');

        return $this->renderer->render($response, 'listprops.phtml', [
            'title' => 'properties',
            'pageName' => 'Property List',
            'data' => $data,
        ]);

    }

    public function delete(Request $request, Response $response){

    }
}