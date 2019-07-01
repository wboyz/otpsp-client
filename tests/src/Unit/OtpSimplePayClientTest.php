<?php

declare(strict_types = 1);

namespace Cheppers\OtpClient\Tests\Unit;

use Cheppers\OtpClient\DataType\InstantDeliveryNotification;
use Cheppers\OtpClient\DataType\InstantOrderStatus;
use Cheppers\OtpClient\DataType\InstantRefundNotification;
use Cheppers\OtpClient\OtpSimplePayClient;
use Cheppers\OtpClient\Serializer;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * @covers \Cheppers\OtpClient\OtpSimplePayClient
 */
class OtpSimplePayClientTest extends TestCase
{

    public function casesInstantOrderStatusPost()
    {
        return [
            'basic' => [
                InstantOrderStatus::__set_state([
                    'ORDER_DATE' => 'myOrderDate',
                    'REFNO' => 'myRefNo',
                    'REFNOEXT' => 'myRefNoExt',
                    'ORDER_STATUS' => 'MyOrderStatus',
                    'PAYMETHOD' => 'myPayMethod',
                ]),
                implode(PHP_EOL, [
                    '<?xml version="1.0" encoding="UTF-8"?>',
                    '<Order>',
                    '<ORDER_DATE>myOrderDate</ORDER_DATE>',
                    '<REFNO>myRefNo</REFNO>',
                    '<REFNOEXT>myRefNoExt</REFNOEXT>',
                    '<ORDER_STATUS>MyOrderStatus</ORDER_STATUS>',
                    '<PAYMETHOD>myPayMethod</PAYMETHOD>',
                    '<HASH>myHash</HASH>',
                    '</Order>'
                ]),
                'foo',
            ],
            'not found' => [
                null,
                implode(PHP_EOL, [
                    '<?xml version="1.0" encoding="UTF-8"?>',
                    '<Order>',
                    '<ERROR_CODE>5011</ERROR_CODE>',
                    '<HASH>myHash</HASH>',
                    '<ORDER_STATUS>NOT_FOUND</ORDER_STATUS>',
                    '</Order>'
                ]),
                'foo',
            ],
        ];
    }

    /**
     * @dataProvider casesInstantOrderStatusPost
     */
    public function testInstantOrderStatusPost(?InstantOrderStatus $expected, string $responseBody, string $refNoExt)
    {
        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'application/xml'],
                $responseBody
            ),
            new RequestException('Error Communicating with Server', new Request('GET', 'order/ios.php'))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client([
            'handler' => $handlerStack,
        ]);

        /** @var \Cheppers\OtpClient\Serializer|\PHPUnit\Framework\MockObject\MockObject $serializer */
        $serializer = $this
            ->getMockBuilder(Serializer::class)
            ->getMock();
        $serializer
            ->expects($this->any())
            ->method('encode')
            ->willReturn('myHash');

        $logger = new NullLogger();
        $actual = (new OtpSimplePayClient($client, $serializer, $logger))
            ->setSecretKey('')
            ->setMerchantId('')
            ->instantOrderStatusPost($refNoExt);

        static::assertEquals($expected, $actual);

        /** @var \GuzzleHttp\Psr7\Request $request */
        $request = $container[0]['request'];
        static::assertEquals(1, count($container));
        static::assertEquals('POST', $request->getMethod());
        static::assertEquals(['application/x-www-form-urlencoded'], $request->getHeader('Content-type'));
        static::assertEquals(['sandbox.simplepay.hu'], $request->getHeader('Host'));
        static::assertEquals(
            'https://sandbox.simplepay.hu/payment/order/ios.php',
            (string) $request->getUri()
        );
    }

    public function casesInstantOrderStatusPostError()
    {
        return [
            'hash mismatch' => [
                [
                    'class' => \Exception::class,
                    'message' => 'Invalid hash',
                    'code' => 1,
                ],
                implode(PHP_EOL, [
                    '<?xml version="1.0" encoding="UTF-8"?>',
                    '<Order>',
                    '<ERROR_CODE>5011</ERROR_CODE>',
                    '<HASH>something else</HASH>',
                    '<ORDER_STATUS>NOT_FOUND</ORDER_STATUS>',
                    '</Order>'
                ]),
                'foo',
            ],
            'unknown' => [
                [
                    'class' => \Exception::class,
                    'message' => 'myOrderStatus',
                    'code' => 42,
                ],
                implode(PHP_EOL, [
                    '<?xml version="1.0" encoding="UTF-8"?>',
                    '<Order>',
                    '<ERROR_CODE>42</ERROR_CODE>',
                    '<HASH>myHash</HASH>',
                    '<ORDER_STATUS>myOrderStatus</ORDER_STATUS>',
                    '</Order>'
                ]),
                'foo',
            ],
        ];
    }

    /**
     * @dataProvider casesInstantOrderStatusPostError
     */
    public function testInstantOrderStatusPostError(array $expected, string $responseBody, string $refNoExt)
    {
        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'application/xml'],
                $responseBody
            ),
            new RequestException('Error Communicating with Server', new Request('GET', 'order/ios.php'))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client([
            'handler' => $handlerStack,
        ]);

        /** @var \Cheppers\OtpClient\Serializer|\PHPUnit\Framework\MockObject\MockObject $serializer */
        $serializer = $this
            ->getMockBuilder(Serializer::class)
            ->getMock();
        $serializer
            ->expects($this->any())
            ->method('encode')
            ->willReturn('myHash');

        static::expectException($expected['class']);
        static::expectExceptionMessage($expected['message']);
        static::expectExceptionCode($expected['code']);
        $logger = new NullLogger();
        (new OtpSimplePayClient($client, $serializer, $logger))
            ->setSecretKey('')
            ->setMerchantId('')
            ->instantOrderStatusPost($refNoExt);
    }

    public function casesInstantRefundNotificationPost()
    {
        return [
            'basic' => [
                InstantRefundNotification::__set_state([
                    'ORDER_REF' => 'myOrderRef',
                    'STATUS_CODE' => '1',
                    'STATUS_NAME' => 'myStatusName',
                    'IRN_DATE' => 'myIrnDate',
                ]),
                '<epayment>myOrderRef|1|myStatusName|myIrnDate|myHash</epayment>',
                'foo',
                'var',
                'bar',
                'baz',
            ],
        ];
    }

    /**
     * @dataProvider casesInstantRefundNotificationPost
     */
    public function testInstantRefundNotificationPost(
        ?InstantRefundNotification $expected,
        string $responseBody,
        string $orderRef,
        string $orderAmount,
        string $orderCurrency,
        string $refundAmount
    ) {
        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'application/xml'],
                $responseBody
            ),
            new RequestException('Error Communicating with Server', new Request('GET', 'order/irn.php'))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client([
            'handler' => $handlerStack,
        ]);

        /** @var \Cheppers\OtpClient\Serializer|\PHPUnit\Framework\MockObject\MockObject $serializer */
        $serializer = $this
            ->getMockBuilder(Serializer::class)
            ->getMock();
        $serializer
            ->expects($this->any())
            ->method('encode')
            ->willReturn('myHash');

        $logger = new NullLogger();
        $actual = (new OtpSimplePayClient($client, $serializer, $logger))
            ->setSecretKey('')
            ->setMerchantId('')
            ->instantRefundNotificationPost(
                $orderRef,
                $orderAmount,
                $orderCurrency,
                $refundAmount
            );

        static::assertEquals($expected, $actual);

        /** @var \GuzzleHttp\Psr7\Request $request */
        $request = $container[0]['request'];
        static::assertEquals(1, count($container));
        static::assertEquals('POST', $request->getMethod());
        static::assertEquals(['application/x-www-form-urlencoded'], $request->getHeader('Content-type'));
        static::assertEquals(['sandbox.simplepay.hu'], $request->getHeader('Host'));
        static::assertEquals(
            'https://sandbox.simplepay.hu/payment/order/irn.php',
            (string) $request->getUri()
        );
    }

    public function casesInstantRefundNotificationPostError()
    {
        return [
            'hash mismatch' => [
                [
                    'class' => \Exception::class,
                    'message' => 'Invalid hash',
                    'code' => 1,
                ],
                '<epayment>myOrderRef|1|myStatusName|myIrnDate|myOtherHash</epayment>',
                'foo',
                'var',
                'bar',
                'baz',
            ],
            'wrong status code' => [
                [
                    'class' => \Exception::class,
                    'message' => 'invalid status code',
                    'code' => 1,
                ],
                '<epayment>myOrderRef|23|myStatusName|myIrnDate|myHash</epayment>',
                'foo',
                'var',
                'bar',
                'baz',
            ],
        ];
    }

    /**
     * @dataProvider casesInstantRefundNotificationPostError
     */
    public function testInstantRefundNotificationPostError(
        array $expected,
        string $responseBody,
        string $orderRef,
        string $orderAmount,
        string $orderCurrency,
        string $refundAmount
    ) {
        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'application/xml'],
                $responseBody
            ),
            new RequestException('Error Communicating with Server', new Request('GET', 'order/irn.php'))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client([
            'handler' => $handlerStack,
        ]);

        /** @var \Cheppers\OtpClient\Serializer|\PHPUnit\Framework\MockObject\MockObject $serializer */
        $serializer = $this
            ->getMockBuilder(Serializer::class)
            ->getMock();
        $serializer
            ->expects($this->any())
            ->method('encode')
            ->willReturn('myHash');

        static::expectException($expected['class']);
        static::expectExceptionMessage($expected['message']);
        static::expectExceptionCode($expected['code']);
        $logger = new NullLogger();
        (new OtpSimplePayClient($client, $serializer, $logger))
            ->setSecretKey('')
            ->setMerchantId('')
            ->instantRefundNotificationPost($orderRef, $orderAmount, $orderCurrency, $refundAmount);
    }

    public function casesInstantDeliveryNotificationPost()
    {
        return [
            'basic' => [
                InstantDeliveryNotification::__set_state([
                    'ORDER_REF' => 'myOrderRef',
                    'STATUS_CODE' => '1',
                    'STATUS_NAME' => 'myStatusName',
                    'IDN_DATE' => 'myIdnDate',
                ]),
                '<epayment>myOrderRef|1|myStatusName|myIdnDate|myHash</epayment>',
                'foo',
                'var',
                'bar',
            ],
        ];
    }

    /**
     * @dataProvider casesInstantDeliveryNotificationPost
     */
    public function testInstantDeliveryNotificationPost(
        ?InstantDeliveryNotification $expected,
        string $responseBody,
        string $orderRef,
        string $orderAmount,
        string $orderCurrency
    ) {
        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'application/xml'],
                $responseBody
            ),
            new RequestException('Error Communicating with Server', new Request('GET', 'order/idn.php'))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client([
            'handler' => $handlerStack,
        ]);

        /** @var \Cheppers\OtpClient\Serializer|\PHPUnit\Framework\MockObject\MockObject $serializer */
        $serializer = $this
            ->getMockBuilder(Serializer::class)
            ->getMock();
        $serializer
            ->expects($this->any())
            ->method('encode')
            ->willReturn('myHash');

        $logger = new NullLogger();
        $actual = (new OtpSimplePayClient($client, $serializer, $logger))
            ->setSecretKey('')
            ->setMerchantId('')
            ->instantDeliveryNotificationPost(
                $orderRef,
                $orderAmount,
                $orderCurrency
            );

        static::assertEquals($expected, $actual);

        /** @var \GuzzleHttp\Psr7\Request $request */
        $request = $container[0]['request'];
        static::assertEquals(1, count($container));
        static::assertEquals('POST', $request->getMethod());
        static::assertEquals(['application/x-www-form-urlencoded'], $request->getHeader('Content-type'));
        static::assertEquals(['sandbox.simplepay.hu'], $request->getHeader('Host'));
        static::assertEquals(
            'https://sandbox.simplepay.hu/payment/order/idn.php',
            (string) $request->getUri()
        );
    }

    public function casesInstantDeliveryNotificationPostError()
    {
        return [
            'hash mismatch' => [
                [
                    'class' => \Exception::class,
                    'message' => 'Invalid hash',
                    'code' => 1,
                ],
                '<epayment>myOrderRef|1|myStatusName|myIdnDate|myOtherHash</epayment>',
                'foo',
                'var',
                'bar',
                'baz',
            ],
            'wrong status code' => [
                [
                    'class' => \Exception::class,
                    'message' => 'invalid status code',
                    'code' => 1,
                ],
                '<epayment>myOrderRef|23|myStatusName|myIdnDate|myHash</epayment>',
                'foo',
                'var',
                'bar',
                'baz',
            ],
        ];
    }

    /**
     * @dataProvider casesInstantDeliveryNotificationPostError
     */
    public function testInstantDeliveryNotificationPostError(
        array $expected,
        string $responseBody,
        string $orderRef,
        string $orderAmount,
        string $orderCurrency
    ) {
        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'application/xml'],
                $responseBody
            ),
            new RequestException('Error Communicating with Server', new Request('GET', 'order/idn.php'))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client([
            'handler' => $handlerStack,
        ]);

        /** @var \Cheppers\OtpClient\Serializer|\PHPUnit\Framework\MockObject\MockObject $serializer */
        $serializer = $this
            ->getMockBuilder(Serializer::class)
            ->getMock();
        $serializer
            ->expects($this->any())
            ->method('encode')
            ->willReturn('myHash');

        static::expectException($expected['class']);
        static::expectExceptionMessage($expected['message']);
        static::expectExceptionCode($expected['code']);
        $logger = new NullLogger();
        (new OtpSimplePayClient($client, $serializer, $logger))
            ->setSecretKey('')
            ->setMerchantId('')
            ->instantDeliveryNotificationPost($orderRef, $orderAmount, $orderCurrency);
    }

    public function casesValidateStatusCodeSuccess()
    {
        return [
            '200 ok test' => [
                null,
                200
            ],
        ];
    }

    /**
     * @dataProvider casesValidateStatusCodeSuccess
     */
    public function testValidateStatusCodeSuccess($expected, int $statusCode)
    {
        $client = new Client();
        $serializer = new Serializer();
        $logger = new NullLogger();
        $validateMethod = new \ReflectionMethod(OtpSimplePayClient::class, 'validateStatusCode');
        $validateMethod->setAccessible(true);
        $otpClient = new OtpSimplePayClient($client, $serializer, $logger);
        $actual = $validateMethod->invokeArgs($otpClient, [$statusCode]);

        static::assertSame($expected, $actual);
    }

    public function casesValidateStatusCodeFail()
    {
        return [
            '404 not found test' => [
                [
                    'class' => \Exception::class,
                    'message' => 'invalid response code',
                    'code' => 1,
                ],
                404
            ],
        ];
    }

    /**
     * @dataProvider casesValidateStatusCodeFail
     */
    public function testValidateStatusCodeFail(array $expected, int $statusCode)
    {
        $client = new Client();
        $serializer = new Serializer();
        $logger = new NullLogger();
        $validateMethod = new \ReflectionMethod(OtpSimplePayClient::class, 'validateStatusCode');
        $validateMethod->setAccessible(true);
        $otpClient = new OtpSimplePayClient($client, $serializer, $logger);

        static::expectException($expected['class']);
        static::expectExceptionMessage($expected['message']);
        static::expectExceptionCode($expected['code']);
        $validateMethod->invokeArgs($otpClient, [$statusCode]);
    }
}
