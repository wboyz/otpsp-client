<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient;

use Cheppers\OtpspClient\DataType\BackRef;
use Cheppers\OtpspClient\DataType\InstantDeliveryNotification;
use Cheppers\OtpspClient\DataType\InstantOrderStatus;
use Cheppers\OtpspClient\DataType\InstantRefundNotification;
use Cheppers\OtpspClient\DataType\InstantPaymentNotification;
use Cheppers\OtpspClient\DataType\Redirect;
use DateTimeInterface;
use DOMDocument;
use DOMXPath;
use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class OtpSimplePayClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var \Cheppers\OtpspClient\Checksum
     */
    protected $checksum;

    /**
     * @var string
     */
    protected $baseUri = 'https://sandbox.simplepay.hu/payment/v2';

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

    public function startPayment(Redirect $redirect)
    {
        $data = $redirect->exportJsonString();
        $header = [
            'Content-type' => 'application/json',
            'Signature' => $this->checksum->calculate($data, $this->secretKey),
        ];

        $request = new Request('POST', $this->getUri('/start'), $header, $redirect->exportJsonString());

        return $this->client->send($request);
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponseXml(string $xml): array
    {
        // @todo Validate with *.xsd.
        $doc = new DOMDocument();
        $doc->loadXML($xml);
        $rootNode = $doc->childNodes->item(0);

        $values = [];
        $xpath = new DOMXPath($doc);
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
        $doc = new DOMDocument();
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
    public function isValidChecksum(string $expectedHash, string $values): bool
    {
        $actualHash = $this
            ->checksum
            ->calculate($values, $this->getSecretKey());

        return $expectedHash === $actualHash;
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
}
