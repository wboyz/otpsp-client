<?php

namespace Cheppers\OtpspClient\Tests\Unit;

use Cheppers\OtpspClient\Checksum;
use Cheppers\OtpspClient\DataType\PaymentRequest;
use Cheppers\OtpspClient\DataType\StartResponse;
use Cheppers\OtpspClient\OtpSimplePayClient;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class OtpSimplePayClientTest extends TestCase
{

    public function casesStartPayment()
    {
        $paymentRequest = new PaymentRequest();

        return [
            'basic' => [
                StartResponse::__set_state([
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
    public function testStartPayment(StartResponse $expected, string $responseBody, PaymentRequest $paymentRequest)
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
}
