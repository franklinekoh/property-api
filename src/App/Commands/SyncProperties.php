<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Services\Properties;
use GuzzleHttp\Client;
use App\Models\Property;
use App\Models\PropertyTypes;

class SyncProperties extends Command {

    /**
     * total API records
     * In the future this should be gotten from the API and should not static
     *
     * @var int
     */
    protected $totalRecords = 1000;

    /**
     * Maximum number of data that can be returned in a page
     * @var int
     */
    protected $maxPageSize = 100;

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:sync-properties')

            // the short description shown while running "php bin/console list"
            ->setDescription('Sync properties from the property api')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows will automatically sync the properties from the property api into the database.');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeLn("Syncing data...");

        $properties = new Properties(new Client());

        $loopCount  = $this->totalRecords/$this->maxPageSize;
        for($i = 1; $i < $loopCount + 1; $i++){

            $pageSize = $this->maxPageSize;
            $pageNumber = $i;

            try{

                $response = $properties->fetchAll($pageSize, $pageNumber);

                if ($response->getStatusCode() === 200){

                    $arrayResponse =  json_decode($response->getBody(), true);

                    $data = $arrayResponse['data'];

                    foreach ($data as $datum){

                        if (!$this->existsPropertyType($datum['property_type_id'])){
                                PropertyTypes::create([
                                    'id' => $datum['property_type_id'],
                                    'title' => $datum['property_type']['title'],
                                    'description' => $datum['property_type']['description']
                                ]);
                        }

                         Property::updateOrCreate(
                                [
                                    'uuid' => $datum['uuid']
                                ],
                                [
                                    'uuid' => $datum['uuid'],
                                    'county' => $datum['county'],
                                    'country' => $datum['country'],
                                    'town' => $datum['town'],
                                    'description' => $datum['description'],
                                    'address' => $datum['address'],
                                    'img_url' => $datum['image_full'],
                                    'thumbnail_url' => $datum['image_thumbnail'],
                                    'latitude' => $datum['latitude'],
                                    'num_bedrooms' => $datum['num_bedrooms'],
                                    'property_type_id' => $datum['property_type_id'],
                                    'num_bathrooms' => $datum['num_bathrooms'],
                                    'price' => $datum['price'],
                                    'type' => $datum['type']
                                ]
                            );


                    }


                }else{
                    $output->write("Could not Sync data /n status: ".$response->getStatusCode()."/n message: ".$response->getBody());
                }



                if ($i === $loopCount){
                    $output->writeLn("Syncing data completed successfully");
                }
            }catch (e $exception){
                $output->write("An Error Occurred ". $exception->getMessage());
                return 1;
            }

        }


        return 0;
    }

    protected function existsPropertyType($propertyTypeId){

        return PropertyTypes::where('id', $propertyTypeId)->exists();
    }
}