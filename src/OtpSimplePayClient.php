<?php

declare(strict_types = 1);

namespace Cheppers\OtpClient;

use Cheppers\OtpClient\DataType\InstantDeliveryNotification;
use Cheppers\OtpClient\DataType\InstantOrderStatus;
use Cheppers\OtpClient\DataType\InstantRefundNotification;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class OtpSimplePayClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

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
     * @var string
     */
    protected $backRefUrl = '';

    public function getBackRefUrl(): string
    {
        return $this->backRefUrl;
    }

    public function setBackRefUrl(string $backRefUrl)
    {
        $this->backRefUrl = $backRefUrl;

        return $this;
    }

    /**
     * @var string[]
     */
    protected $backRefData = [];

    public function getBackRefData(): array
    {
        return $this->backRefData;
    }

    public function setBackRefData(string $url): array
    {
        $urlParts = parse_url($url);
        $queryVariables = [];
        parse_str($urlParts['query'], $queryVariables);

        return $queryVariables;
    }

    /**
     * @var array
     */
    protected $ipnPostData = [];

    public function __construct(ClientInterface $client, Serializer $serializer, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->setLogger($logger);
    }

    public function instantDeliveryNotificationPost(
        string $orderRef,
        string $orderAmount,
        string $orderCurrency
    ): ?InstantDeliveryNotification {
        $header = [
            'Content-type' => 'application/x-www-form-urlencoded',
        ];

        $body = [
            'MERCHANT' => $this->getMerchantId(),
            'ORDER_REF' => $orderRef,
            'ORDER_AMOUNT' => $orderAmount,
            'ORDER_CURRENCY' => $orderCurrency,
            'IDN_DATE' => date('Y-m-d H:i:s'),
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
        $this->validateStatusCode($statusCode);

        $xml = (string) $response->getBody();
        $values = $this->parseResponseString($xml, 'IDN_DATE');

        $hash = $values['HASH'];
        unset($values['HASH']);

        $this->validateHash($hash, $values);

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
    ): ?InstantRefundNotification {
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
        $this->validateStatusCode($statusCode);

        $xml = (string) $response->getBody();
        $values = $this->parseResponseString($xml, 'IRN_DATE');

        $hash = $values['HASH'];
        unset($values['HASH']);

        $this->validateHash($hash, $values);

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

        $response = $this->client->send($request);

        $statusCode = $response->getStatusCode();
        $this->validateStatusCode($statusCode);

        $xml = (string) $response->getBody();
        $values = $this->parseResponseBody($xml);

        $hash = $values['HASH'];
        unset($values['HASH']);

        $this->validateHash($hash, $values);

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

    protected function parseResponseString(string $xml, string $dateKey)
    {
        $ePayment = [
            'ORDER_REF',
            'STATUS_CODE',
            'STATUS_NAME',
            $dateKey,
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

    protected function getInstantPaymentNotificationResponse(): array
    {
        $serverDate = date('YmdHis');
        $hashArray = [
            $this->ipnPostData['IPN_PID'][0],
            $this->ipnPostData['IPN_PNAME'][0],
            $this->ipnPostData['IPN_DATE'],
            $serverDate,
        ];

        $hash = $this->serializer->encode($hashArray, $this->secretKey);
        $responseBody = '<EPAYMENT>' . $serverDate . '|' . $hash . '</EPAYMENT>';

        return [
            'headers' => [],
            'body' => $responseBody,
            'statusCode' => 200,
        ];
    }

    protected function checkBackRefCtrl(): bool
    {
        if (isset($this->backRefData['ctrl'])) {
            if ($this->backRefData['ctrl'] === $this->serializer->decode($this->backRefUrl, $this->secretKey)) {
                return true;
            }
            return false;
        }

        return false;
    }

    protected function isPaymentSuccess(): bool
    {
        if (isset($this->backRefData['RC'])
            && ($this->backRefData['RC'] === '000'
            || $this->backRefData['RC'] === '001')
        ) {
            return true;
        }

        return false;
    }

    protected function validateStatusCode($statusCode)
    {
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new \Exception('@todo', 1);
        }
    }

    protected function validateHash($hash, $values)
    {
        $isValid = $hash === $this->serializer->encode($values, $this->getSecretKey());

        if (!$isValid) {
            throw new \Exception('Invalid hash', 1);
        }
    }
}
