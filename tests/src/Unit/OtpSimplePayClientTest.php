<?php

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
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class OtpSimplePayClientTest extends TestCase
{

    public function casesStartPayment()
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
                json_encode([
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
            ]
        ];
    }

    /**
     * @dataProvider casesStartPayment
     */
    public function testStartPayment(PaymentResponse $expected, string $responseBody, PaymentRequest $paymentRequest)
    {
        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(
                200,
                [
                    'Content-Type' => 'application/json;charset=UTF-8',
                    'Signature' => 'mySignature',
                ],
                $responseBody
            )]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client([
            'handler' => $handlerStack,
        ]);
        /** @var \Cheppers\OtpspClient\Checksum|\PHPUnit\Framework\MockObject\MockObject $serializer */
        $serializer = $this
            ->getMockBuilder(Checksum::class)
            ->getMock();
        $serializer
            ->expects($this->any())
            ->method('calculate')
            ->willReturn('mySignature');
        $logger = new NullLogger();
        $dateTime = new DateTime();
        $actual = (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
            ->setSecretKey('')
            ->startPayment($paymentRequest);
        static::assertEquals($expected, $actual);
        /** @var \GuzzleHttp\Psr7\Request $request */
        $request = $container[0]['request'];
        static::assertEquals(1, count($container));
        static::assertEquals('POST', $request->getMethod());
        static::assertEquals(['application/json'], $request->getHeader('Content-type'));
        static::assertEquals(['sandbox.simplepay.hu'], $request->getHeader('Host'));
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
                json_encode([
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
                $refundRequest,
            ]
        ];
    }

    /**
     * @dataProvider casesStartRefund
     */
    public function testStartRefund(RefundResponse $expected, string $responseBody, RefundRequest $refundRequest)
    {
        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(
                200,
                [
                    'Content-Type' => 'application/json;charset=UTF-8',
                    'Signature' => 'mySignature',
                ],
                $responseBody
            )]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client([
            'handler' => $handlerStack,
        ]);
        /** @var \Cheppers\OtpspClient\Checksum|\PHPUnit\Framework\MockObject\MockObject $serializer */
        $serializer = $this
            ->getMockBuilder(Checksum::class)
            ->getMock();
        $serializer
            ->expects($this->any())
            ->method('calculate')
            ->willReturn('mySignature');
        $logger = new NullLogger();
        $dateTime = new DateTime();
        $actual = (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
            ->setSecretKey('')
            ->startRefund($refundRequest);
        static::assertEquals($expected, $actual);
        /** @var \GuzzleHttp\Psr7\Request $request */
        $request = $container[0]['request'];
        static::assertEquals(1, count($container));
        static::assertEquals('POST', $request->getMethod());
        static::assertEquals(['application/json'], $request->getHeader('Content-type'));
        static::assertEquals(['sandbox.simplepay.hu'], $request->getHeader('Host'));
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
     * @throws \Exception
     */
    public function testParseBackResponse(BackResponse $expected, string $url)
    {
        $logger = new NullLogger();
        $serializer = new Checksum();
        $dateTime = new DateTime();
        $client = new Client();
        $actual = (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
            ->setSecretKey('')
            ->parseBackResponse($url);
        static::assertEquals($expected, $actual);
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
        $serializer = new Checksum();
        $dateTime = new DateTime();
        $client = new Client();
        $actual = (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
            ->setSecretKey('')
            ->parseInstantPaymentNotificationRequest($request);
        static::assertEquals($expected, $actual);
    }
}
