<?php

declare(strict_types = 1);

namespace Cheppers\OtpClient;

use Cheppers\OtpClient\DataType\InstantDeliveryNotification;
use Cheppers\OtpClient\DataType\InstantOrderStatus;
use Cheppers\OtpClient\DataType\InstantRefundNotification;
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

    /**
     * @var array
     */
    protected $ipnPostData = [];

    public function __construct(ClientInterface $client, Serializer $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    public function instantDeliveryNotificationPost(
        string $orderRef,
        string $orderAmount,
        string $orderCurrency
    ) {
        $header = [
            'Content-type' => 'application/x-www-form-urlencoded',
        ];

        $body = [
            'MERCHANT' => $this->getMerchantId(),
            'ORDER_REF' => $orderRef,
            'ORDER_AMOUNT' => $orderAmount,
            'ORDER_CURRENCY' => $orderCurrency,
            'IRN_DATE' => date('Y-m-d H:i:s'),
        ];

        $body['ORDER_HASH'] = $this->serializer->encode(array_values($body), $this->getSecretKey());

        $request = new Request(
            'POST',
            $this->getUri('order/idn.php'),
            $header,
            http_build_query($body)
        );

        $response = $this->client->send($request);

        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new \Exception('@todo', 1);
        }

        $xml = (string) $response->getBody();
        $values = $this->parseResponseString($xml);

        $hash = $values['HASH'];
        unset($values['HASH']);

        $isValid = $hash === $this->serializer->encode($values, $this->getSecretKey());
        if (!$isValid) {
            throw new \Exception('@todo', 1);
        }

        if ($values['STATUS_CODE'] !== '1') {
            throw new \Exception('@todo', 1);
        }

        return InstantDeliveryNotification::__set_state($values);
    }

    public function instantRefundNotificationPost(
        string $orderRef,
        string $orderAmount,
        string $orderCurrency,
        string $refundAmount
    ) {
        $header = [
            'Content-type' => 'application/x-www-form-urlencoded',
        ];

        $body = [
            'MERCHANT' => $this->getMerchantId(),
            'ORDER_REF' => $orderRef,
            'ORDER_AMOUNT' => $orderAmount,
            'ORDER_CURRENCY' => $orderCurrency,
            'IRN_DATE' => date('Y-m-d H:i:s'),
            'AMOUNT' => $refundAmount
        ];

        $body['ORDER_HASH'] = $this->serializer->encode(array_values($body), $this->getSecretKey());

        $request = new Request(
            'POST',
            $this->getUri('order/irn.php'),
            $header,
            http_build_query($body)
        );

        $response = $this->client->send($request);

        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new \Exception('@todo', 1);
        }

        $xml = (string) $response->getBody();
        $values = $this->parseResponseString($xml);

        $hash = $values['HASH'];
        unset($values['HASH']);

        $isValid = $hash === $this->serializer->encode($values, $this->getSecretKey());
        if (!$isValid) {
            throw new \Exception('@todo', 1);
        }

        if ($values['STATUS_CODE'] !== '1') {
            throw new \Exception('@todo', 1);
        }

        return InstantRefundNotification::__set_state($values);
    }
    
    public function instantOrderStatusPost(string $refNoExt): ?InstantOrderStatus
    {
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
        $values = $this->parseResponseBody($xml);
        // @todo Check HASH key.
        $hash = $values['HASH'];
        unset($values['HASH']);

        $isValid = $hash === $this->serializer->encode($values, $this->getSecretKey());
        if (!$isValid) {
            throw new \Exception('@todo', 1);
        }

        if (array_key_exists('ERROR_CODE', $values)) {
            switch ($values['ERROR_CODE']) {
                case '5011':
                    // Not found.
                    return null;
            }

            throw new \Exception(
                $values['ORDER_STATUS'] ?? 'Unknown error',
                (int) $values['ERROR_CODE']
            );
        }

        return InstantOrderStatus::__set_state($values);
    }

    protected function parseResponseBody(string $xml): array
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

    protected function parseResponseString(string $xml)
    {
        $ePayment = [
            'ORDER_REF',
            'STATUS_CODE',
            'STATUS_NAME',
            'IRN_DATE',
            'HASH',
        ];

        $doc = new \DOMDocument();
        $doc->loadXML($xml);
        $rootNode = $doc->childNodes->item(0);

        $values = explode('|', $rootNode->nodeValue);
        return array_combine($ePayment, $values);
    }

    protected function getUri(string $path): string
    {
        return $this->getBaseUri() . "/$path";
    }

    protected function flatArray(array $array = [], array $skip = []): array
    {
        if (count($array) === 0) {
            return [];
        }
        $return = [];
        foreach ($array as $name => $item) {
            if (!in_array($name, $skip)) {
                if (is_array($item)) {
                    foreach ($item as $subItem) {
                        $return[] = $subItem;
                    }
                } elseif (!is_array($item)) {
                    $return[] = $item;
                }
            }
        }
        return $return;
    }

    protected function instantPaymentNotificationValidate(string $requestBody): bool
    {
        parse_str($requestBody, $this->ipnPostData);
        if (count($this->ipnPostData) < 1 || !array_key_exists('REFNOEXT', $this->ipnPostData)) {
            return false;
        }

        $calculatedHash = $this->serializer->encode($this->flatArray($this->ipnPostData, ['HASH']), $this->secretKey);

        return $calculatedHash === $this->ipnPostData['HASH'];
    }

    protected function instantPaymentNotificationConfirmReceived(): string
    {
        $serverDate = @date('YmdHis');
        $hashArray = [
            $this->ipnPostData['IPN_PID'][0],
            $this->ipnPostData['IPN_PNAME'][0],
            $this->ipnPostData['IPN_DATE'],
            $serverDate,
        ];

        $hash = $this->serializer->encode($hashArray, $this->secretKey);
        $responseBody = '<EPAYMENT>' . $serverDate . '|' . $hash . '</EPAYMENT>';

        return $responseBody;
    }
}
