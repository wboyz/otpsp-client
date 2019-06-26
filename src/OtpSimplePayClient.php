<?php

declare(strict_types = 1);

namespace Cheppers\OtpClient;

use Cheppers\OtpClient\DataType\InstantOrderStatus;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;

class OtpSimplePayClient
{

    /**
     * @var \Cheppers\OtpClient\Serializer|null
     */
    protected $serializer = null;

    /**
     * @var string
     */
    protected $baseUri = 'https://sandbox.simplepay.hu/payment';

    /**
     * {@inheritdoc}
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseUri($value)
    {
        $this->baseUri = $value;

        return $this;
    }

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client = null;

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * @return $this
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @var string
     */
    protected $merchantId = '';

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @return $this
     */
    public function setMerchantId(string $merchantId)
    {
        $this->merchantId = $merchantId;

        return $this;
    }

    /**
     * @var string
     */
    protected $secretKey = '';

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    /**
     * @return $this
     */
    public function setSecretKey(string $secretKey)
    {
        $this->secretKey = $secretKey;

        return $this;
    }

    public function __construct(ClientInterface $client, Serializer $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    public function instantOrderStatusPost(string $refNoExt): InstantOrderStatus {
        $header = [
            'Content-type' => 'application/x-www-form-urlencoded',
        ];

        $body =  [
            'MERCHANT' => $this->getMerchantId(),
            'REFNOEXT' => $refNoExt,
        ];

        $body['HASH'] = $this->serializer->encode(array_values($body), $this->getSecretKey());

        $request = new Request(
            'POST',
            $this->getUri('order/ios.php'),
            $header,
            http_build_query($body)
        );

        // @todo Check response status code.
        $response = $this->client->send($request);

        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new \Exception('@todo', 1);
        }

        $xml = (string) $response->getBody();
        $values = $this->getBodyValues($xml);
        // @todo Check HASH key.
        $hash = $values['HASH'];
        unset($values['HASH']);

        $isValid = $hash === $this->serializer->encode($values, $this->getSecretKey());
        if (!$isValid) {
            throw new \Exception('@todo', 1);
        }

        if (array_key_exists('ERROR_CODE', $values)) {
            throw new \Exception(
                $values['ORDER_STATUS'] ?? 'Unknown error',
                (int) $values['ERROR_CODE']
            );
        }

        return InstantOrderStatus::__set_state($values);
    }

    protected function getBodyValues(string $xml): array
    {
        $doc = new \DOMDocument();
        $doc->loadXML($xml);
        $rootNode = $doc->childNodes->item(0);

        $values = [];
        $xpath = new \DOMXPath($doc);
        /** @var \DOMNode $node */
        foreach ($xpath->query('./*', $rootNode) as $node) {
            $values[$node->nodeName] = $node->textContent;
        }

        return $values;
    }

    protected function getUri(string $path): string
    {
        return $this->getBaseUri() . "/$path";
    }

}
