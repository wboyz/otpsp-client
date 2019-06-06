<?php

declare(strict_types=1);

namespace Cheppers\OtpClient;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class Communicator
{
    /**
     * @var Client
     */
    public $client;

    /**
     * @var string
     */
    public $baseUri = 'https://sandbox.simplepay.hu/payment/';

    /**
     * @var ResponseInterface
     */
    public $response;

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    public function setBaseUri(string $value): void
    {
        $this->baseUri = $value;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }


    public function sendPost(string $path, array $options = []): Communicator
    {
        return $this->sendRequest('POST', $path, $options);
    }

    public function sendRequest(string $method, string $path, array $options): Communicator
    {
        $uri = $this->getBaseUri() . "/$path";
        $this->response = $this->client->request($method, $uri, $options);

        return $this;
    }
}
