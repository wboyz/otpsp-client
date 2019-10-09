<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit;

use Cheppers\OtpspClient\Checksum;
use Cheppers\OtpspClient\DataType\BackResponse;
use Cheppers\OtpspClient\DataType\InstantPaymentNotification;
use Cheppers\OtpspClient\DataType\PaymentRequest;
use Cheppers\OtpspClient\DataType\PaymentResponse;
use Cheppers\OtpspClient\DataType\RefundRequest;
use Cheppers\OtpspClient\DataType\RefundResponse;
use Cheppers\OtpspClient\OtpSimplePayClient;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * @covers \Cheppers\OtpspClient\OtpSimplePayClient<extended>
 */
class OtpSimplePayClientTest extends TestCase
{

    public function casesStartPayment(): array
    {
        $paymentRequest = new PaymentRequest();

        return [
            'basic' => [
                PaymentResponse::__set_state([
                    'salt' => 'bwivfsgm8aSmiSTyZ0FYILvu2wgO0NKe',
                    'merchant' => 'test-merchant',
                    'orderRef' => 'test-order-ref',
                    'currency' => 'HUF',
                    'transactionId' => 9999999,
                    'timeout' => '2019-09-07T22:51:13+02:00',
                    'total' => 9255,
                    'paymentUrl' => 'test-url.com'
                ]),
                $paymentRequest,
                [
                    'body' => json_encode([
                        'salt' => 'bwivfsgm8aSmiSTyZ0FYILvu2wgO0NKe',
                        'merchant' => 'test-merchant',
                        'orderRef' => 'test-order-ref',
                        'currency' => 'HUF',
                        'transactionId' => 9999999,
                        'timeout' => '2019-09-07T22:51:13+02:00',
                        'total' => 9255,
                        'paymentUrl' => 'test-url.com'
                    ]),
                ],
            ]
        ];
    }

    /**
     * @dataProvider casesStartPayment
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testStartPayment(
        PaymentResponse $expected,
        PaymentRequest $paymentRequest,
        array $responseData,
        bool $checksumVerify = true
    ) {
        $container = [];
        $otpClient = $this->createOptSimplePayClient($container, $responseData, $checksumVerify);
        static::assertEquals($expected, $otpClient->startPayment($paymentRequest));

        /** @var \Psr\Http\Message\RequestInterface $request */
        $request = $container[0]['request'];
        static::assertEquals(1, count($container));
        static::assertEquals('POST', $request->getMethod());
        static::assertEquals(['application/json'], $request->getHeader('Content-type'));
        static::assertEquals(['sandbox.simplepay.hu'], $request->getHeader('Host'));
        static::assertEquals(json_encode($paymentRequest), $request->getBody()->getContents());
        static::assertEquals(
            'https://sandbox.simplepay.hu/payment/v2/start',
            (string) $request->getUri()
        );
    }

    public function casesStartRefund()
    {
        $refundRequest = new RefundRequest();

        return [
            'basic' => [
                RefundResponse::__set_state([
                    'salt' => 'bwivfsgm8aSmiSTyZ0FYILvu2wgO0NKe',
                    'merchant' => 'test-merchant',
                    'orderRef' => 'test-order-ref',
                    'currency' => 'HUF',
                    'transactionId' => 9999998,
                    'refundTransactionId' => 99999999,
                    'refundTotal' => 9255,
                    'remainingTotal' => 0,
                ]),
                $refundRequest,
                [
                    'body' => json_encode([
                        'refundTransactionId' => 99999999,
                        'salt' => 'bwivfsgm8aSmiSTyZ0FYILvu2wgO0NKe',
                        'merchant' => 'test-merchant',
                        'remainingTotal' => 0,
                        'orderRef' => 'test-order-ref',
                        'currency' => 'HUF',
                        'transactionId' => 9999998,
                        'refundTotal' => 9255,
                        'sdkVersion' => 'SimplePay_PHP_SDK_2.0_180930:33ccd5ed8e8a965d18abfae333404184',
                    ]),
                ],
            ]
        ];
    }

    /**
     * @dataProvider casesStartRefund
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testStartRefund(
        RefundResponse $expected,
        RefundRequest $refundRequest,
        array $responseData,
        bool $checksumVerify = true
    ) {
        $container = [];
        $otpClient = $this->createOptSimplePayClient($container, $responseData, $checksumVerify);
        $actual = $otpClient->startRefund($refundRequest);

        static::assertEquals($expected, $actual);
        /** @var \GuzzleHttp\Psr7\Request $request */
        $request = $container[0]['request'];
        static::assertEquals(1, count($container));
        static::assertEquals('POST', $request->getMethod());
        static::assertEquals(['application/json'], $request->getHeader('Content-type'));
        static::assertEquals(['sandbox.simplepay.hu'], $request->getHeader('Host'));
        static::assertEquals(json_encode($refundRequest), $request->getBody()->getContents());
        static::assertEquals(
            'https://sandbox.simplepay.hu/payment/v2/refund',
            (string) $request->getUri()
        );
    }

    public function casesParseBackResponse()
    {
        $backResponse = new BackResponse();
        $backResponse->merchant = 'test-merchant';
        $backResponse->event = 'SUCCESS';
        $backResponse->transactionId = 99999999;
        $backResponse->responseCode = 0;
        $backResponse->orderId = 'test-order-id';

        return [
            'basic' => [
                $backResponse,
                implode('', [
                    'http://test.io/en/?',
                    'r=eyJyIjowLCJ0Ijo5OTk5OTk5OSwiZSI6IlNVQ0NFU1MiLCJtIjoidGV',
                    'zdC1tZXJjaGFudCIsIm8iOiJ0ZXN0LW9yZGVyLWlkIn0=&',
                    's=nZSWubLZFBHZl0ylAyLcWzsQ6NoD0fX3UMXrTt13/vNjsQfV8L/URUyYBWx3X6TZ',
                ]),
            ],
        ];
    }

    /**
     * @dataProvider casesParseBackResponse
     *
     * @throws Exception
     */
    public function testParseBackResponse(BackResponse $expected, string $url)
    {
        $logger = new NullLogger();
        $serializer = new Checksum();
        $client = new Client();
        $actual = (new OtpSimplePayClient($client, $serializer, $logger))
            ->setSecretKey('')
            ->parseBackResponse($url);
        static::assertEquals($expected, $actual);
    }

    public function casesParseBackResponseFailed()
    {
        return [
            'r and s variable not exist' => [
                new Exception('Invalid response'),
                'http://test.io/en/?t=test&f=failed',
            ],
            'invalid signature' => [
                new Exception('Invalid response'),
                'http://test.io/en/?r=response-body&s=signature',
            ],
            'invalid response' => [
                new Exception('Response message is not a valid JSON', 4),
                implode('', [
                    'http://test.io/en/?',
                    'r=dGVzdC1tZXNzYWdl&',
                    's=2KpxBOqk5HmIHQLBbKDYbkC9nQnjhK3EBmGqtjyKF4lccLiIxknVz/RJpW2IgjGf',
                ]),
            ],
        ];
    }

    /**
     * @dataProvider casesParseBackResponseFailed
     *
     * @throws Exception
     */
    public function testParseBackResponseFailed(Exception $expected, string $url)
    {
        $logger = new NullLogger();
        $serializer = new Checksum();
        $client = new Client();

        static::expectExceptionObject($expected);
        (new OtpSimplePayClient($client, $serializer, $logger))
            ->setSecretKey('')
            ->parseBackResponse($url);
    }

    public function casesParseInstantPaymentNotificationRequest()
    {
        return [
            'basic' => [
                InstantPaymentNotification::__set_state([
                    'salt' => 'test-salt',
                    'orderRef' => 'test-orderRef',
                    'method' => 'test-card',
                    'merchant' => 'test-merchant',
                    'finishDate' => 'test-finishDate',
                    'paymentDate' => 'test-paymentDate',
                    'transactionId' => 42,
                    'status' => 'test-status',
                ]),
                new Request(
                    'POST',
                    'test-uri.com',
                    [
                        'Content-Type' => 'application/json',
                        'Signature' => 'jRLcA9EYhm+xjfyXCJ9ft/OUuhgtRR5Ct2IQYCXAlTGtubvn7kBsBmp/5K2ExlGi',
                    ],
                    json_encode([
                        'salt' => 'test-salt',
                        'orderRef' => 'test-orderRef',
                        'method' => 'test-card',
                        'merchant' => 'test-merchant',
                        'finishDate' => 'test-finishDate',
                        'paymentDate' => 'test-paymentDate',
                        'transactionId' => 42,
                        'status' => 'test-status',
                    ])
                ),
            ],
        ];
    }

    /**
     * @dataProvider casesParseInstantPaymentNotificationRequest
     *
     * @throws \Exception
     */
    public function testParseInstantPaymentNotificationRequest(InstantPaymentNotification $expected, Request $request)
    {
        $logger = new NullLogger();
        /** @var \Cheppers\OtpspClient\Checksum|\PHPUnit\Framework\MockObject\MockObject $checksum */
        $checksum = $this
            ->getMockBuilder(Checksum::class)
            ->getMock();
        $checksum
            ->expects($this->any())
            ->method('verify')
            ->willReturn(true);
        $client = new Client();
        $actual = (new OtpSimplePayClient($client, $checksum, $logger))
            ->setSecretKey('')
            ->parseInstantPaymentNotificationRequest($request);
        static::assertEquals($expected, $actual);
    }

    public function casesParseInstantPaymentNotificationMessage(): array
    {
        return [
            'basic' => [
                $this->getBaseInstantPaymentNotification(),
                'jRLcA9EYhm+xjfyXCJ9ft/OUuhgtRR5Ct2IQYCXAlTGtubvn7kBsBmp/5K2ExlGi',
                json_encode([
                    'method'        => 'CARD',
                    'finishDate'    => '2019-09-01T00:12:42+02:00',
                    'paymentDate'   => '2019-09-02T00:12:42+02:00',
                    'status'        => 'FINISHED',
                    'salt'          => 'test-salt',
                    'merchant'      => 'test-merchant',
                    'orderRef'      => 'test-order-ref',
                    'transactionId' => 42,
                ]),
            ],
        ];
    }

    /**
     * @dataProvider casesParseInstantPaymentNotificationMessage
     */
    public function testParseInstantPaymentNotificationMessage(
        InstantPaymentNotification $expected,
        string $signature,
        string $bodyContent
    ) {
        $logger = new NullLogger();
        /** @var \Cheppers\OtpspClient\Checksum|\PHPUnit\Framework\MockObject\MockObject $checksum */
        $checksum = $this
            ->getMockBuilder(Checksum::class)
            ->getMock();
        $checksum
            ->expects($this->any())
            ->method('verify')
            ->willReturn(true);
        $client = new Client();
        $actual = (new OtpSimplePayClient($client, $checksum, $logger))
            ->setSecretKey('')
            ->parseInstantPaymentNotificationMessage($signature, $bodyContent);
        static::assertEquals($expected, $actual);
    }

    public function casesParseIpnMessageFailed(): array
    {
        return [
            'invalid signature' => [
                new Exception('Response checksum mismatch'),
                'wrong-signature',
                'body-content'
            ],
            'invalid body' => [
                new Exception('Response body is not a valid JSON', 5),
                'rKj0k6d8P0ksIMLKO2tPzYJzY0iCKlV4RTlx5ACUHV86xfV18FcjtC28BRcs2DHy',
                'invalid-body-content'
            ],
        ];
    }

    /**
     * @dataProvider casesParseIpnMessageFailed
     */
    public function testParseIpnMessageFailed(Exception $expected, string $signature, string $bodyContent): void
    {
        $logger = new NullLogger();
        /** @var \Cheppers\OtpspClient\Checksum|\PHPUnit\Framework\MockObject\MockObject $checksum */
        $checksum = new Checksum();
        $client = new Client();

        static::expectExceptionObject($expected);
        (new OtpSimplePayClient($client, $checksum, $logger))
            ->setSecretKey('')
            ->parseInstantPaymentNotificationMessage($signature, $bodyContent);
    }

    public function casesGetInstantPaymentNotificationSuccessResponse(): array
    {

        return [
            'basic' => [
                new Response(
                    200,
                    [
                        'Content-Type' => 'application/json',
                        'Signature' => '469yavaC3GOVWHKbyIAA35iL8yXTj4zlEhVZqmMPye/i72u1Mq05LcvFr3EgGP0I',
                    ],
                    json_encode([
                        'method'        => 'CARD',
                        'finishDate'    => '2019-09-01T00:12:42+02:00',
                        'paymentDate'   => '2019-09-02T00:12:42+02:00',
                        'status'        => 'FINISHED',
                        'receiveDate'   => '2019-09-03T00:12:42+02:00',
                        'salt'          => 'test-salt',
                        'merchant'      => 'test-merchant',
                        'orderRef'      => 'test-order-ref',
                        'transactionId' => 42,
                    ])
                ),
                $this->getBaseInstantPaymentNotification(),
            ],
        ];
    }

    /**
     * @dataProvider casesGetInstantPaymentNotificationSuccessResponse
     */
    public function testGetInstantPaymentNotificationSuccessResponse(
        Response $expected,
        InstantPaymentNotification $ipn
    ): void {
        $guzzle = new Client();
        $checksum = new Checksum();
        $logger = new NullLogger();
        $response = (new OtpSimplePayClient($guzzle, $checksum, $logger))
            ->setSecretKey('')
            ->setNow(new DateTime('2019-09-03T00:12:42+02:00'))
            ->getInstantPaymentNotificationSuccessResponse($ipn);

        static::assertSame($expected->getStatusCode(), $response->getStatusCode());
        static::assertSame($expected->getHeaders(), $response->getHeaders());
        static::assertSame($expected->getBody()->getContents(), $response->getBody()->getContents());
    }

    public function casesGetIpnSuccessMessage(): array
    {
        return [
            'basic' => [
                [
                    'statusCode' => 200,
                    'body' => json_encode([
                        'method'        => 'CARD',
                        'finishDate'    => '2019-09-01T00:12:42+02:00',
                        'paymentDate'   => '2019-09-02T00:12:42+02:00',
                        'status'        => 'FINISHED',
                        'receiveDate'   => '2019-09-03T00:12:42+02:00',
                        'salt'          => 'test-salt',
                        'merchant'      => 'test-merchant',
                        'orderRef'      => 'test-order-ref',
                        'transactionId' => 42,
                    ]),
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Signature' => '469yavaC3GOVWHKbyIAA35iL8yXTj4zlEhVZqmMPye/i72u1Mq05LcvFr3EgGP0I',
                    ],
                ],
                $this->getBaseInstantPaymentNotification(),
            ],
        ];
    }

    /**
     * @dataProvider casesGetIpnSuccessMessage
     */
    public function testGetIpnSuccessMessage(array $expected, InstantPaymentNotification $ipn): void
    {
        $guzzle = new Client();
        $checksum = new Checksum();
        $logger = new NullLogger();
        $actual = (new OtpSimplePayClient($guzzle, $checksum, $logger))
            ->setSecretKey('')
            ->setNow(new DateTime('2019-09-03T00:12:42+02:00'))
            ->getIpnSuccessMessage($ipn);

        static::assertSame($expected, $actual);
    }

    protected function createOptSimplePayClient(
        array &$requestContainer,
        array $responseData,
        $checksumVerify = true
    ): OtpSimplePayClient {
        $responseData = array_replace_recursive(
            [
                'status' => 200,
                'headers' => [
                    'Content-Type' => 'application/json;charset=UTF-8',
                    'Signature' => 'mySignature',
                ],
                'body' => '',
            ],
            $responseData
        );
        $responseData['headers'] = array_filter($responseData['headers']);

        $history = Middleware::history($requestContainer);
        $mock = new MockHandler([
            new Response(...array_values($responseData)),
            new RequestException('Error Communicating with Server', new Request('GET', 'ping'))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client([
            'handler' => $handlerStack,
        ]);

        /** @var \Cheppers\OtpspClient\Checksum|\PHPUnit\Framework\MockObject\MockObject $checksum */
        $checksum = $this
            ->getMockBuilder(Checksum::class)
            ->getMock();
        $checksum
            ->expects($this->any())
            ->method('verify')
            ->willReturn($checksumVerify);
        $logger = new NullLogger();

        return new OtpSimplePayClient($client, $checksum, $logger);
    }

    protected function getBaseInstantPaymentNotification(): InstantPaymentNotification
    {
        $ipn = new InstantPaymentNotification();
        $ipn->salt = 'test-salt';
        $ipn->orderRef = 'test-order-ref';
        $ipn->method = 'CARD';
        $ipn->merchant = 'test-merchant';
        $ipn->finishDate = '2019-09-01T00:12:42+02:00';
        $ipn->paymentDate = '2019-09-02T00:12:42+02:00';
        $ipn->transactionId = 42;
        $ipn->status = 'FINISHED';

        return $ipn;
    }
}
