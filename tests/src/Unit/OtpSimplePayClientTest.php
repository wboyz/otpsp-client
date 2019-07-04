<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit;

use Cheppers\OtpspClient\DataType\Backref;
use Cheppers\OtpspClient\DataType\InstantDeliveryNotification;
use Cheppers\OtpspClient\DataType\InstantOrderStatus;
use Cheppers\OtpspClient\DataType\InstantRefundNotification;
use Cheppers\OtpspClient\DataType\Ipn;
use Cheppers\OtpspClient\OtpSimplePayClient;
use Cheppers\OtpspClient\Checksum;
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
 * @covers \Cheppers\OtpspClient\OtpSimplePayClient
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

        /** @var \Cheppers\OtpspClient\Checksum|\PHPUnit\Framework\MockObject\MockObject $serializer */
        $serializer = $this
            ->getMockBuilder(Checksum::class)
            ->getMock();
        $serializer
            ->expects($this->any())
            ->method('calculate')
            ->willReturn('myHash');
        
        $logger = new NullLogger();
        $dateTime = new \DateTime();
        $actual = (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
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

        /** @var \Cheppers\OtpspClient\Checksum|\PHPUnit\Framework\MockObject\MockObject $serializer */
        $serializer = $this
            ->getMockBuilder(Checksum::class)
            ->getMock();
        $serializer
            ->expects($this->any())
            ->method('calculate')
            ->willReturn('myHash');

        static::expectException($expected['class']);
        static::expectExceptionMessage($expected['message']);
        static::expectExceptionCode($expected['code']);
        
        $logger = new NullLogger();
        $dateTime = new \DateTime();
        (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
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

        /** @var \Cheppers\OtpspClient\Checksum|\PHPUnit\Framework\MockObject\MockObject $serializer */
        $serializer = $this
            ->getMockBuilder(Checksum::class)
            ->getMock();
        $serializer
            ->expects($this->any())
            ->method('calculate')
            ->willReturn('myHash');

        $logger = new NullLogger();
        $dateTime = new \DateTime();
        $actual = (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
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
                    'message' => 'myStatusName',
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

        /** @var \Cheppers\OtpspClient\Checksum|\PHPUnit\Framework\MockObject\MockObject $serializer */
        $serializer = $this
            ->getMockBuilder(Checksum::class)
            ->getMock();
        $serializer
            ->expects($this->any())
            ->method('calculate')
            ->willReturn('myHash');

        static::expectException($expected['class']);
        static::expectExceptionMessage($expected['message']);
        static::expectExceptionCode($expected['code']);
        
        $logger = new NullLogger();
        $dateTime = new \DateTime();
        (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
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

        /** @var \Cheppers\OtpspClient\Checksum|\PHPUnit\Framework\MockObject\MockObject $serializer */
        $serializer = $this
            ->getMockBuilder(Checksum::class)
            ->getMock();
        $serializer
            ->expects($this->any())
            ->method('calculate')
            ->willReturn('myHash');
        $logger = new NullLogger();
        $dateTime = new \DateTime();
        $actual = (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
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
                    'message' => 'myStatusName',
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

        /** @var \Cheppers\OtpspClient\Checksum|\PHPUnit\Framework\MockObject\MockObject $serializer */
        $serializer = $this
            ->getMockBuilder(Checksum::class)
            ->getMock();
        $serializer
            ->expects($this->any())
            ->method('calculate')
            ->willReturn('myHash');

        static::expectException($expected['class']);
        static::expectExceptionMessage($expected['message']);
        static::expectExceptionCode($expected['code']);
        
        $logger = new NullLogger();
        $dateTime = new \DateTime();
        (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
            ->setSecretKey('')
            ->setMerchantId('')
            ->instantDeliveryNotificationPost($orderRef, $orderAmount, $orderCurrency);
    }

    public function casesValidateResponseStatusCodeSuccess()
    {
        return [
            '200 ok test' => [
                OtpSimplePayClient::class,
                200
            ],
        ];
    }

    /**
     * @dataProvider casesValidateResponseStatusCodeSuccess
     */
    public function testValidateResponseStatusCodeSuccess($expected, int $statusCode)
    {
        $client = new Client();
        $serializer = new Checksum();
        $logger = new NullLogger();
        $dateTime = new \DateTime();
        $actual = (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
            ->validateResponseStatusCode($statusCode);

        static::assertInstanceOf($expected, $actual);
    }

    public function casesValidateResponseStatusCodeFail()
    {
        return [
            '404 not found test' => [
                [
                    'class' => \Exception::class,
                    'message' => 'Invalid response code',
                    'code' => 1,
                ],
                404
            ],
        ];
    }

    /**
     * @dataProvider casesValidateResponseStatusCodeFail
     */
    public function testValidateResponseStatusCodeFail(array $expected, int $statusCode)
    {
        $client = new Client();
        $serializer = new Checksum();
        $logger = new NullLogger();
        $dateTime = new \DateTime();

        static::expectException($expected['class']);
        static::expectExceptionMessage($expected['message']);
        static::expectExceptionCode($expected['code']);
        (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
            ->validateResponseStatusCode($statusCode);
    }

    public function casesValidateStatusCodeSuccess()
    {
        return [
            '1 ok test' => [
                OtpSimplePayClient::class,
                [
                    'STATUS_CODE' => '1',
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesValidateStatusCodeSuccess
     */
    public function testValidateStatusCodeSuccess($expected, array $values)
    {
        $client = new Client();
        $serializer = new Checksum();
        $logger = new NullLogger();
        $dateTime = new \DateTime();
        $actual = (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
            ->validateStatusCode($values);

        static::assertInstanceOf($expected, $actual);
    }

    public function casesValidateStatusCodeFail()
    {
        return [
            'not 1, fail' => [
                [
                    'class' => \Exception::class,
                    'message' => 'MyStatusName',
                    'code' => 1,
                ],
                [
                    'STATUS_CODE' => '3',
                    'STATUS_NAME' => 'MyStatusName',
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesValidateStatusCodeFail
     */
    public function testValidateStatusCodeFail($expected, array $values)
    {
        $client = new Client();
        $serializer = new Checksum();
        $logger = new NullLogger();
        $dateTime = new \DateTime();

        static::expectException($expected['class']);
        static::expectExceptionMessage($expected['message']);
        static::expectExceptionCode($expected['code']);
        (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
            ->validateStatusCode($values);
    }

    public function casesIsInstantPaymentNotificationValid()
    {
        return [
            'valid' => [
                true,
                'REFNOEXT=1&HASH=bef91610dda7aabfe371623edb399f3e',
            ],
            'invalid' => [
                false,
                ''
            ],
        ];
    }

    /**
     * @dataProvider casesIsInstantPaymentNotificationValid
     */
    public function testIsInstantPaymentNotificationValid(bool $expected, string $requestBody)
    {
        $client = new Client();
        $serializer = new Checksum();
        $logger = new NullLogger();
        $dateTime = new \DateTime();
        $actual = (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
            ->isInstantPaymentNotificationValid($requestBody);

        static::assertSame($expected, $actual);
    }


    public function casesGetInstantPaymentNotificationSuccessResponse()
    {
        return [
            'ok test' => [
                [
                    'headers' => [
                        'Content-Type' => 'application/xml'
                    ],
                    'body' => '<EPAYMENT>date|20cc2d06b49a9082117397c4ecd6496c</EPAYMENT>',
                    'statusCode' => 200,
                ],
                [
                    'IPN_PID' => '1',
                    'IPN_PNAME' => '2',
                    'IPN_DATE' => '3',
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesGetInstantPaymentNotificationSuccessResponse
     */
    public function testGetInstantPaymentNotificationSuccessResponse(array $expected, array $ipnPostData)
    {
        $client = new Client();
        $serializer = new Checksum();
        $logger = new NullLogger();
        $dateTime = $this->createMock(\DateTime::class);
        $dateTime
            ->method('format')
            ->willReturn('date');
        $actual = (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
            ->setIpnPostData($ipnPostData)
            ->getInstantPaymentNotificationSuccessResponse();

        static::assertSame($expected, $actual);
    }

    public function casesGetInstantPaymentNotificationFailedResponse()
    {
        return [
            'ok test' => [
                [
                    'headers' => [
                        'Content-Type' => 'text/plain',
                    ],
                    'body' => 'Instant Payment Notification cannot be processed',
                    'statusCode' => 503,
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesGetInstantPaymentNotificationFailedResponse
     */
    public function testGetInstantPaymentNotificationFailedResponse(array $expected)
    {
        $client = new Client();
        $serializer = new Checksum();
        $logger = new NullLogger();
        $dateTime = $this->createMock(\DateTime::class);
        $dateTime
            ->method('format')
            ->willReturn('date');
        $actual = (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
            ->getInstantPaymentNotificationFailedResponse();

        static::assertSame($expected, $actual);
    }

    public function casesIsPaymentSuccess()
    {
        return [
            'ok test' => [
                true,
                '000',
            ],
            'bad test' => [
                false,
                '100',
            ],
            'bad test 2' => [
                false,
                'bar',
            ],
        ];
    }

    /**
     * @dataProvider casesIsPaymentSuccess
     */
    public function testIsPaymentSuccess(bool $expected, string $returnCode)
    {
        $client = new Client();
        $serializer = new Checksum();
        $logger = new NullLogger();
        $dateTime = new \DateTime();
        $actual = (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
            ->isPaymentSuccess($returnCode);

        static::assertSame($expected, $actual);
    }

    public function casesParseBackRefRequest()
    {
        return [
            'query string test' => [
                Backref::__set_state([
                    'RC' => '000',
                    'RT' => '000 | OK',
                    '3dsecure' => 'NO',
                    'date' => '2016-04-20 14:57:38',
                    'payrefno' => '99016530',
                    'ctrl' => 'a5a268fd200eaef93e87a3f1403ce65f',
                ]),
                'https://weboldalam.tld/backref.php'
                . '?order_ref=101010514611570269664&order_currency=HUF&RC=000'
                . '&RT=000+%7C+OK&3dsecure=NO&date=2016-04-20+14%3A57%3A38'
                . '&payrefno=99016530&ctrl=a5a268fd200eaef93e87a3f1403ce65f',
                '',
            ],
        ];
    }

    /**
     * @dataProvider casesParseBackRefRequest
     */
    public function testParseBackRefRequest(Backref $expected, string $url, string $body)
    {
        $client = new Client();
        $serializer = new Checksum();
        $logger = new NullLogger();
        $dateTime = new \DateTime();
        $actual = (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
            ->parseBackRefRequest($url, $body);

        static::assertEquals($expected, $actual);
    }

    public function casesParseInstantPaymentNotificationRequest()
    {
        return [
            'test1' => [
                Ipn::__set_state([
                    'REFNO' => '1234',
                    'REFNOEXT' => '1111',
                    'ORDERSTATUS' => 'ok',
                    'IPN_PID' => [
                        '55',
                        '66',
                    ],
                    'IPN_PNAME' => [
                        'hello',
                        'there',
                    ],
                    'IPN_DATE' => '20170214085902',
                    'HASH' => 'myHash',
                ]),
                '',
                'REFNO=1234&REFNOEXT=1111&ORDERSTATUS=ok'
                . '&IPN_PID[0]=55&IPN_PID[1]=66'
                . '&IPN_PNAME[0]=hello&IPN_PNAME[1]=there'
                . '&IPN_DATE=20170214085902&HASH=myHash'
            ],
        ];
    }

    /**
     * @dataProvider casesParseInstantPaymentNotificationRequest
     */
    public function testParseInstantPaymentNotificationRequest(Ipn $expected, string $url, string $body)
    {
        $client = new Client();
        $serializer = new Checksum();
        $logger = new NullLogger();
        $dateTime = new \DateTime();
        $actual = (new OtpSimplePayClient($client, $serializer, $logger, $dateTime))
            ->parseInstantPaymentNotificationRequest($url, $body);

        static::assertEquals($expected, $actual);
    }
}
