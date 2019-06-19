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
    protected $client;

    /**
     * @var ResponseInterface
     */
    public $response;

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function sendPost(string $url, array $options = []): Communicator
    {
        return $this->sendRequest('POST', $url, $options);
    }

    public function sendRequest(string $method, string $url, array $options): Communicator
    {
        $this->response = $this->client->request($method, $url, $options);

        return $this;
    }
}
