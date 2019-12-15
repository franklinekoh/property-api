<?php
namespace App\Services;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;

class Properties {

    /**
     * Guzzle http client
     * @var
     */

    protected $httpClient;


    /**
     *
     * @var string
     */
    protected static $propertyUrl = 'api/properties';


    /**
     * Properties constructor.
     * @param $httpClient
     */
    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }


    /**
     * Fetches all property
     *
     * @param $pageSize
     * @param $pageNumber
     * @return \Psr\Http\Message\ResponseInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchAll($pageSize, $pageNumber){

        $url = getenv('MTC_URL').self::$propertyUrl;

        try{
            $response = $this->httpClient->request('GET', $url, [
                'verify' => false,
                'query' => [
                    'api_key' => getenv('MTC_API_KEY'),
                    'page[size]' => $pageSize,
                    'page[number]' => $pageNumber
                ]
            ]);
        }catch (BadResponseException $exception){
            $response = $exception->getResponse();
        }

        return $response;
    }
}