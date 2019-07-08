<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient;

use Cheppers\OtpspClient\DataType\BackRef;
use Cheppers\OtpspClient\DataType\InstantDeliveryNotification;
use Cheppers\OtpspClient\DataType\InstantOrderStatus;
use Cheppers\OtpspClient\DataType\InstantRefundNotification;
use Cheppers\OtpspClient\DataType\InstantPaymentNotification;
use DateTimeInterface;
use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class OtpSimplePayClient implements LoggerAwareInterface, OtpSimplePayClientInterface
{
    use LoggerAwareTrait;

    /**
     * @var \Cheppers\OtpspClient\Checksum
     */
    protected $checksum;

    /**
     * @var string
     */
    protected $baseUri = 'https://sandbox.simplepay.hu/payment';

    /**
     * {@inheritdoc}
     */
    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseUri(string $value)
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

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    /**
     * @return $this
     */
    public function setDateTime(DateTimeInterface $dateTime)
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
     * @var string[]
     */
    protected $supportedLanguages = [
        'cz',
        'de',
        'en',
        'es',
        'it',
        'hr',
        'hu',
        'pl',
        'ro',
        'sk',
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
        Checksum $serializer,
        LoggerInterface $logger,
        DateTimeInterface $dateTime
    ) {
        $this->client = $client;
        $this->checksum = $serializer;
        $this->setLogger($logger);
        $this->dateTime = $dateTime;
    }

    /**
     * {@inheritdoc}
     */
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

        $body['ORDER_HASH'] = $this->checksum->calculate(array_values($body), $this->getSecretKey());

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

    /**
     * {@inheritdoc}
     */
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

        $body['ORDER_HASH'] = $this->checksum->calculate(array_values($body), $this->getSecretKey());

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

    /**
     * {@inheritdoc}
     */
    public function instantOrderStatusPost(string $refNoExt): ?InstantOrderStatus
    {
        $header = [
            'Content-type' => 'application/x-www-form-urlencoded',
        ];

        $body =  [
            'MERCHANT' => $this->getMerchantId(),
            'REFNOEXT' => $refNoExt,
        ];

        $body['HASH'] = $this->checksum->calculate(array_values($body), $this->getSecretKey());

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
        $values = $this->parseResponseXml($xml);

        $hash = $values['HASH'];
        unset($values['HASH']);

        $this->validateHash($hash, $values);

        if (array_key_exists('ERROR_CODE', $values)) {
            switch ($values['ERROR_CODE']) {
                case static::STATUS_CODE_NOT_FOUND:
                    return null;
            }

            throw new Exception(
                $values['ORDER_STATUS'] ?? 'Unknown error',
                (int) $values['ERROR_CODE']
            );
        }

        return InstantOrderStatus::__set_state($values);
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponseXml(string $xml): array
    {
        // @todo Validate with *.xsd.
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

    /**
     * {@inheritdoc}
     */
    public function parseResponseString(string $xml, string $dateKey): array
    {
        $ePayment = [
            'ORDER_REF',
            'STATUS_CODE',
            'STATUS_NAME',
            $dateKey,
            'HASH',
        ];

        // @todo Validate with *.xsd.
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

    public function getLiveUpdateUrl(): string
    {
        return $this->getUri('order/lu.php');
    }

    /**
     * {@inheritdoc}
     */
    public function parseInstantPaymentNotificationRequest(string $body): InstantPaymentNotification
    {
        // @todo Parse requests by url, headers and body.
        $values = [];
        parse_str($body, $values);

        return InstantPaymentNotification::__set_state($values);
    }

    /**
     * {@inheritdoc}
     */
    public function isValidChecksum(string $expectedHash, array $values): bool
    {
        $actualHash = $this
            ->checksum
            ->calculate($values, $this->getSecretKey());

        return $expectedHash === $actualHash;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstantPaymentNotificationSuccessResponse(InstantPaymentNotification $ipn): array
    {
        $serverDate = $this->getDateTime()->format('YmdHis');
        $hashArray = [
            $ipn->ipnPId[0],
            $ipn->ipnPName[0],
            $ipn->ipnDate,
            $serverDate,
        ];

        $hash = $this->checksum->calculate($hashArray, $this->getSecretKey());
        $responseBody = '<EPAYMENT>' . $serverDate . '|' . $hash . '</EPAYMENT>';

        return [
            'headers' => [
                'Content-Type' => 'application/xml',
            ],
            'body' => $responseBody,
            'statusCode' => 200,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getInstantPaymentNotificationFailedResponse(): array
    {
        return [
            'headers' => [
                'Content-Type' => 'text/plain',
            ],
            'body' => 'Instant Payment Notification cannot be processed',
            'statusCode' => 503,
        ];
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

    /**
     * {@inheritdoc}
     */
    public function isPaymentSuccess(string $returnCode): bool
    {
        return in_array($returnCode, $this->getSuccessReturnCodes());
    }

    /**
     * {@inheritdoc}
     */
    public function validateStatusCode(array $values)
    {
        if ($values['STATUS_CODE'] != static::STATUS_CODE_SUCCESS) {
            throw new Exception($values['STATUS_NAME'], 1);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validateHash(string $hash, array $values)
    {
        if ($hash !== $this->checksum->calculate($values, $this->getSecretKey())) {
            throw new Exception('Invalid hash', 1);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validateResponseStatusCode(int $statusCode)
    {
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new Exception('Invalid response code', 1);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function parseBackRefRequest(string $url): BackRef
    {
        // @todo Parse requests by url, headers and body.
        $values = [];
        $queryString = parse_url($url, PHP_URL_QUERY);
        parse_str($queryString, $values);

        return BackRef::__set_state($values);
    }
}
