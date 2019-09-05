<?php

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\RefundRequest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Cheppers\OtpspClient\DataType\RefundRequest<extended>
 */
class RefundRequestTest extends RequestBaseTestBase
{
    protected $className = RefundRequest::class;

    public function casesSetState()
    {
        return [
            'empty' => [new RefundRequest(), []],
            'basic' => [
                $this->getBaseRefundRequest(),
                [
                    'merchant' => 'test-merchant',
                    'orderRef' => 'test-order-ref',
                    'salt' => 'test-salt',
                    'currency' => 'test-currency',
                    'sdkVersion' => 'test-sdk-version',
                    'transactionId' => 'test-transaction-id',
                    'refundTotal' => 42.5,
                ]
            ],
        ];
    }

    public function casesJsonSerialize()
    {
        return [
            'empty' => [
                [
                    'transactionId' => '',
                    'refundTotal' => 0.0,
                    'merchant' => '',
                    'orderRef' => '',
                    'salt' => '',
                    'currency' => '',
                    'sdkVersion' => 'SimplePay_PHP_SDK_2.0_180930:33ccd5ed8e8a965d18abfae333404184',
                ],
                new RefundRequest()
            ],
            'basic' => [
                [
                    'transactionId' => 'test-transaction-id',
                    'refundTotal' => 42.5,
                    'merchant' => 'test-merchant',
                    'orderRef' => 'test-order-ref',
                    'salt' => 'test-salt',
                    'currency' => 'test-currency',
                    'sdkVersion' => 'test-sdk-version',
                ],
                $this->getBaseRefundRequest(),
            ],
        ];
    }

    protected function getBaseRefundRequest(): RefundRequest
    {
        $refundRequest = new RefundRequest();
        $refundRequest->merchant = 'test-merchant';
        $refundRequest->orderRef = 'test-order-ref';
        $refundRequest->salt = 'test-salt';
        $refundRequest->currency = 'test-currency';
        $refundRequest->sdkVersion = 'test-sdk-version';
        $refundRequest->transactionId = 'test-transaction-id';
        $refundRequest->refundTotal = 42.5;

        return $refundRequest;
    }
}
