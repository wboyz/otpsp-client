<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\RefundResponse;

/**
 * @covers \Cheppers\OtpspClient\DataType\RefundResponse<extended>
 */
class RefundResponseTest extends ResponseBaseTestBase
{
    protected $className = RefundResponse::class;

    public function casesSetState(): array
    {
        $refundResponse = new RefundResponse();
        $refundResponse->transactionId = 36;
        $refundResponse->merchant = 'test-merchant';
        $refundResponse->orderRef = 'test-order-ref';
        $refundResponse->salt = 'test-salt';
        $refundResponse->currency = 'test-currency';
        $refundResponse->refundTotal = 45.5;
        $refundResponse->refundTransactionId = 42;
        $refundResponse->remainingTotal = 47.5;

        return [
            'empty' => [new RefundResponse(), []],
            'basic' => [
                $refundResponse,
                [
                    'transactionId' => 36,
                    'merchant' => 'test-merchant',
                    'orderRef' => 'test-order-ref',
                    'salt' => 'test-salt',
                    'currency' => 'test-currency',
                    'refundTotal' => 45.5,
                    'refundTransactionId' => 42,
                    'remainingTotal' => 47.5,
                ],
            ],
        ];
    }
}
