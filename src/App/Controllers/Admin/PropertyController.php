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

    /**
     * Show property list
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function show(Request $request, Response $response){

        $data = Property::with(['propertyType'])
                    ->orderBy('created_at', 'DESC')
                        ->get();

        $this->renderer->setLayout('layout.phtml');

        return $this->renderer->render($response, 'admin/listprops.phtml', [
            'title' => 'properties',
            'pageName' => 'Property List',
            'data' => $data,
        ]);

    }

    /**
     * Delete property endpoint
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function delete(Request $request, Response $response){

        $this->validator->validateArray($data = $request->getParams(),
            [
                'uuid' => v::notEmpty(),
            ]);

        if ($this->validator->failed()) {
            return $response->withJson(['errors' => $this->validator->getErrors()], 422);
        }

        $deleted = Property::destroy($request->getParam('uuid'));

        if (!$deleted){
            return $response->withJson([
                'status' => false,
                'message' => 'deletion was not successful'
            ]);
        }

        return $response->withJson([
            'status' => true,
            'message' => 'deletion successful'
        ]);

    }
}