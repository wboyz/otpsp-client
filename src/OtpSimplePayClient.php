<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient;

use Cheppers\OtpspClient\DataType\BackResponse;
use Cheppers\OtpspClient\DataType\InstantPaymentNotification;
use Cheppers\OtpspClient\DataType\PaymentRequest;
use Cheppers\OtpspClient\DataType\RefundRequest;
use Cheppers\OtpspClient\DataType\RefundResponse;
use Cheppers\OtpspClient\DataType\RequestBase;
use Cheppers\OtpspClient\DataType\PaymentResponse;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class OtpSimplePayClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

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
    protected $client;

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
     * @var \Cheppers\OtpspClient\ChecksumInterface
     */
    protected $checksum;

    public function getChecksum(): ChecksumInterface
    {
        return $this->checksum;
    }

    /**
     * @return $this
     */
    public function setChecksum(ChecksumInterface $checksum)
    {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * @var string
     */
    protected $dateTimeClass = \DateTime::class;

    public function getDateTimeClass(): string
    {
        return $this->dateTimeClass;
    }

    /**
     * @return $this
     */
    public function setDateTimeClass(string $dateTimeClass)
    {
        $this->dateTimeClass = $dateTimeClass;

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
        ChecksumInterface $checksumCalculator,
        LoggerInterface $logger
    ) {
        $this
            ->setClient($client)
            ->setChecksum($checksumCalculator)
            ->setLogger($logger);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function startPayment(PaymentRequest $paymentRequest): PaymentResponse
    {
        $response = $this->sendRequest($paymentRequest, 'start');
        $body = $this->getMessageBody($response);

        return PaymentResponse::__set_state($body);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function startRefund(RefundRequest $refundRequest): RefundResponse
    {
        $response = $this->sendRequest($refundRequest, 'refund');
        $body = $this->getMessageBody($response);

        return RefundResponse::__set_state($body);
    }

    /**
     * @throws \Exception
     */
    public function parseBackResponse(string $url): BackResponse
    {
        $values = Utils::getQueryFromUrl($url);

        if (!array_key_exists('r', $values) || !array_key_exists('s', $values)) {
            throw new \Exception('Invalid response');
        }

        $responseMessage = base64_decode($values['r']);

        if (!$this->getChecksum()->verify($this->getSecretKey(), $responseMessage, $values['s'])) {
            throw new \Exception('Invalid response');
        }

        $body = json_decode($responseMessage, true);
        if (!is_array($body)) {
            throw new \Exception('Response message is not a valid JSON', 4);
        }

        return BackResponse::__set_state($body);
    }

    /**
     * @throws \Exception
     */
    public function parseInstantPaymentNotificationRequest(RequestInterface $request): ?InstantPaymentNotification
    {
        $body = $this->getMessageBody($request);

        return InstantPaymentNotification::__set_state($body);
    }

    public function getInstantPaymentNotificationSuccessResponse(
        InstantPaymentNotification $instantPaymentNotification
    ): ResponseInterface {
        if (empty($instantPaymentNotification->receiveDate)) {
            /** @var \DateTimeInterface $now */
            $now = new $this->dateTimeClass('now');
            $instantPaymentNotification->receiveDate = $now->format('Y-m-d\TH:i:sP');
        }

        $message = json_encode($instantPaymentNotification);

        return new Response(
            200,
            [
                'Content-Type' => 'application/json',
                'Signature' => $this->getChecksum()->calculate($this->secretKey, $message),
            ],
            $message
        );
    }

    protected function getUri(string $path): string
    {
        return $this->getBaseUri() . "/$path";
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendRequest(RequestBase $requestType, string $path): ResponseInterface
    {
        $requestMessage = json_encode($requestType->jsonSerialize());

        $header = [
            'Content-type' => 'application/json',
            'Signature' => $this->getChecksum()->calculate($this->secretKey, $requestMessage),
        ];

        return $this->client->send(new Request('POST', $this->getUri($path), $header, $requestMessage));
    }

    protected function getMessageBody(MessageInterface $message): array
    {
        if (!$message->hasHeader('Content-Type')) {
            throw new \Exception('Missing header Content-Type', 1);
        }

        $allowedContentTypes = [
            'application/json;charset=UTF-8',
            'application/json',
        ];
        if (!array_intersect($allowedContentTypes, $message->getHeader('Content-Type'))) {
            throw new \Exception('Not allowed Content-Type', 2);
        }

        if (!$message->hasHeader('signature')) {
            throw new \Exception('Response has no signature', 3);
        }

        $signature = $message->getHeader('signature')[0];
        $bodyContent = $message->getBody()->getContents();
        if (!$this->getChecksum()->verify($this->getSecretKey(), $bodyContent, $signature)) {
            throw new \Exception('Response checksum mismatch', 4);
        }

        $body = json_decode($bodyContent, true);
        if (!is_array($body)) {
            throw new \Exception('Response body is not a valid JSON', 5);
        }

        return $body;
    }
}
