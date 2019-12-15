<?php

namespace App\Controllers\Admin;

use phpDocumentor\Reflection\Types\Null_;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;
use Psr\Container\ContainerInterface;
use App\Models\Property;
use App\Utilities\Upload;
use App\Models\PropertyTypes;
use Ramsey\Uuid\Uuid;


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


    protected $imagePath = 'uploads/images/';

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

        $properties = Property::with(['property_type'])
                    ->orderBy('created_at', 'DESC')
                        ->get();

        $propertyTypes = PropertyTypes::all();

        $this->renderer->setLayout('layout.phtml');

        return $this->renderer->render($response, 'admin/listprops.phtml', [
            'title' => 'properties',
            'pageName' => 'Property List',
            'properties' => $properties,
            'propertyTypes' =>  $propertyTypes
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

    /**
     * Create property endpoint
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function store(Request $request, Response $response){

        $data = $request->getParams();

        $uploadedFiles = $request->getUploadedFiles();

        $file = $_FILES['file'];
        $file['maxFileSize'] = '2MB';
        $file['file'] = $_FILES['file']['tmp_name'];

        $validator = $this->validateCreatePropertyRequest($data, $file);
        if ($validator->failed()) {
            return $response->withJson(['errors' => $this->validator->getErrors()], 422);
        }


        $upload = new Upload();

        $fileName = $upload->uploadImage($uploadedFiles['file']);

        $path = $this->imagePath.$fileName;

        Property::create([
            'uuid' => Uuid::uuid1(),
            'county' => $data['county'],
            'country' => $data['country'],
            'town' => $data['town'],
            'description' => $data['description'],
            'address' => $data['address'],
            'img_url' => $path,
            'postcode' => $data['postcode'],
            'property_type_id' => $data['propertyTypeId'],
            'num_bedrooms' => $data['bedrooms'],
            'num_bathrooms' => $data['bathrooms'],
            'price' => $data['price'],
            'type' => $data['type']
        ]);

        return $response->withJson(['status' => true, 'message' => 'Property created successfully']);

    }


    /**
     * Update property api
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public  function update(Request $request, Response $response){

        $data = $request->getParams();

        $uploadedFiles = $request->getUploadedFiles();

        $file = $_FILES['file'];
        $file['maxFileSize'] = '2MB';
        $file['file'] = $_FILES['file']['tmp_name'];

        $validator = $this->validateEditPropertyRequest($data, $file);
        if ($validator->failed()) {
            return $response->withJson(['errors' => $this->validator->getErrors()], 422);
        }

        $property = Property::where('uuid', $data['uuid'])
            ->first();

        if ($property === null){
            return $response->withJson(['errors' => 'invalid property id'], 404);
        }

        $upload = new Upload();

        $fileName = $upload->uploadImage($uploadedFiles['file']);

        $path = $this->imagePath.$fileName;

        $oldFilePath = ROOT.'public/'.$property->img_url;

        if (file_exists($oldFilePath)){
            unlink($oldFilePath);
        }

        $property->update([
            'county' => $data['county'],
            'country' => $data['country'],
            'town' => $data['town'],
            'description' => $data['description'],
            'address' => $data['address'],
            'img_url' => $path,
            'postcode' => $data['postcode'],
            'property_type_id' => $data['propertyTypeId'],
            'num_bedrooms' => $data['bedrooms'],
            'num_bathrooms' => $data['bathrooms'],
            'price' => $data['price'],
            'type' => $data['type']
        ]);

        return $response->withJson(['status' => true, 'message' => 'Property updated successfully']);
    }

    /**
     * Endpoint to get property by uuid
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getPropertyById(Request $request, Response $response, array $args){

        $data['property'] = Property::where('uuid', $args['uuid'])->with(['property_type'])
            ->orderBy('created_at', 'DESC')
            ->first();


        $data['propertyTypes'] = PropertyTypes::all();


        return $response->withJson(['status' => true, 'data' => $data]);

    }


    /**
     * Validate create property request
     * @param $values
     * @param $file
     * @return mixed
     */
    protected function validateCreatePropertyRequest($values, $file){

        $fileValidator = $this->validator->validateArray($file, [
            'file'=>v::image()->size(null, $file['maxFileSize']),
        ]);

        if ($fileValidator->failed())
            return $fileValidator;

        return  $this->validator->validateArray($values, [
                    'country' =>   v::notEmpty()
                                        ->noWhitespace(),
                    'county' => v::notEmpty()
                                     ->noWhitespace(),
                    'town' => v::notEmpty()
                                     ->noWhitespace(),
                    'postcode' => v::notEmpty()
                                        ->noWhitespace(),
                    'description' => v::notEmpty(),
                    'address' => v::notEmpty(),
                    'bathrooms' => v::notEmpty(),
                    'bedrooms' => v::notEmpty(),
                    'price' => V::notEmpty()->numeric(),
                    'propertyTypeId' => v::notEmpty()->numeric(),
                    'type' => v::notEmpty(),

        ]);

    }


    /**
     * Validate Edit property request
     *
     * @param $values
     * @param $file
     * @return mixed
     */
    protected function validateEditPropertyRequest($values, $file){

        $fileValidator = $this->validator->validateArray($file, [
            'file' => v::image()->size(null, $file['maxFileSize']),
        ]);

        if ($fileValidator->failed())
            return $fileValidator;

        return  $this->validator->validateArray($values, [
            'country' =>   v::notEmpty()
                ->noWhitespace(),
            'county' => v::notEmpty()
                ->noWhitespace(),
            'town' => v::notEmpty()
                ->noWhitespace(),
            'postcode' => v::notEmpty()
                ->noWhitespace(),
            'description' => v::notEmpty(),
            'address' => v::notEmpty(),
            'bathrooms' => v::notEmpty(),
            'bedrooms' => v::notEmpty(),
            'price' => V::notEmpty()->numeric(),
            'propertyTypeId' => v::notEmpty()->numeric(),
            'type' => v::notEmpty(),
            'uuid' => v::notEmpty()
        ]);

    }
}