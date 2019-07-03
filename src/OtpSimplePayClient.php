<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient;

use Cheppers\OtpspClient\DataType\InstantDeliveryNotification;
use Cheppers\OtpspClient\DataType\InstantOrderStatus;
use Cheppers\OtpspClient\DataType\InstantRefundNotification;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class OtpSimplePayClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    const RETURN_CODE_SUCCESS = '000';

    const RETURN_CODE_SUCCESS_1 = '001';

    const STATUS_CODE_SUCCESS = 1;

    const STATUS_CODE_NOT_FOUND = 5011;

    /**
     * @var \Cheppers\OtpspClient\Serializer
     */
    protected $serializer;

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
     * @var \DateTimeInterface
     */
    protected $dateTime;

    public function getDateTime(): \DateTimeInterface
    {
        return $this->dateTime;
    }

    /**
     * @return $this
     */
    public function setDateTime(\DateTimeInterface $dateTime)
    {
        $this->dateTime = $dateTime;

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
     * @var array
     */
    protected $ipnPostData = [];

    public function getIpnPostData(): array
    {
        return $this->ipnPostData;
    }

    /**
     * @return $this
     */
    public function setIpnPostData(array $ipnPostData)
    {
        $this->ipnPostData = $ipnPostData;

        return $this;
    }

    /**
     * @var array
     */
    protected $supportedLanguages = [
        'CZ',
        'DE',
        'EN',
        'ES',
        'IT',
        'HR',
        'HU',
        'PL',
        'RO',
        'SK',
    ];

    /**
     * @return string[]
     */
    public function getSupportedLanguages(): array
    {
        return $this->supportedLanguages;
    }

    /**
     * @return $this
     */
    public function setSupportedLanguages(array $supportedLanguages)
    {
        $this->supportedLanguages = $supportedLanguages;

        return $this;
    }

    public function __construct(
        ClientInterface $client,
        Serializer $serializer,
        LoggerInterface $logger,
        \DateTimeInterface $dateTime
    ) {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->setLogger($logger);
        $this->dateTime = $dateTime;
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
            'IDN_DATE' => $this->getDateTime()->format('Y-m-d H:i:s')
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
        $this->validateResponseStatusCode($statusCode);

        $xml = (string) $response->getBody();
        $values = $this->parseResponseString($xml, 'IDN_DATE');

        $hash = $values['HASH'];
        unset($values['HASH']);

        $this->validateHash($hash, $values);
        $this->validateStatusCode($values);

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
            'IRN_DATE' => $this->getDateTime()->format('Y-m-d H:i:s'),
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
        $this->validateResponseStatusCode($statusCode);

        $xml = (string) $response->getBody();
        $values = $this->parseResponseString($xml, 'IRN_DATE');

        $hash = $values['HASH'];
        unset($values['HASH']);

        $this->validateHash($hash, $values);
        $this->validateStatusCode($values);

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
        $this->validateResponseStatusCode($statusCode);

        $xml = (string) $response->getBody();
        $values = $this->parseResponseBody($xml);

        $hash = $values['HASH'];
        unset($values['HASH']);

        $this->validateHash($hash, $values);

        if (array_key_exists('ERROR_CODE', $values)) {
            switch ($values['ERROR_CODE']) {
                case static::STATUS_CODE_NOT_FOUND:
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

    public function flatArray(array $array = [], array $skip = []): array
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

    public function parseResponseBody(string $xml): array
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

        if (array_key_exists('STATUS_CODE', $values)) {
            settype($values['STATUS_CODE'], 'int');
        }

        return $values;
    }

    public function parseResponseString(string $xml, string $dateKey)
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

    public function getUri(string $path): string
    {
        return $this->getBaseUri() . "/$path";
    }

    public function instantPaymentNotificationValidate(string $requestBody): bool
    {
        parse_str($requestBody, $this->ipnPostData);

        if (count($this->ipnPostData) < 1 || !array_key_exists('REFNOEXT', $this->ipnPostData)) {
            return false;
        }

        $calculatedHash = $this
            ->serializer
            ->encode($this->flatArray($this->ipnPostData, ['HASH']), $this->getSecretKey());

        return $calculatedHash === $this->ipnPostData['HASH'];
    }

    public function getInstantPaymentNotificationResponse(): array
    {
        $serverDate = $this->getDateTime()->format('YmdHis');
        $hashArray = [
            $this->ipnPostData['IPN_PID'][0],
            $this->ipnPostData['IPN_PNAME'][0],
            $this->ipnPostData['IPN_DATE'],
            $serverDate,
        ];

        $hash = $this->serializer->encode($hashArray, $this->getSecretKey());
        $responseBody = '<EPAYMENT>' . $serverDate . '|' . $hash . '</EPAYMENT>';

        return [
            'headers' => [],
            'body' => $responseBody,
            'statusCode' => 200,
        ];
    }

    public function checkBackRefCtrl(string $ctrl): bool
    {
        return ($ctrl === $this->serializer->decode($this->getBackRefUrl(), $this->secretKey));
    }

    /**
     * @return string[]
     */
    public function getSuccessReturnCodes(): array
    {
        return [
            static::RETURN_CODE_SUCCESS,
            static::RETURN_CODE_SUCCESS_1,
        ];
    }

    public function isPaymentSuccess(string $returnCode): bool
    {
        return in_array($returnCode, $this->getSuccessReturnCodes());
    }

    public function validateStatusCode(array $values)
    {
        if ($values['STATUS_CODE'] != static::STATUS_CODE_SUCCESS) {
            throw new \Exception('Invalid status code', 1);
        }

        return $this;
    }

    public function validateHash(string $hash, array $values)
    {
        if ($hash !== $this->serializer->encode($values, $this->getSecretKey())) {
            throw new \Exception('Invalid hash', 1);
        }
    }

    public function validateResponseStatusCode(int $statusCode)
    {
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new \Exception('Invalid response code', 1);
        }
    }
}
