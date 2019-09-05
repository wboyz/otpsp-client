<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\PaymentResponse;

/**
 * @covers \Cheppers\OtpspClient\DataType\PaymentResponse<extended>
 */
class PaymentResponseTest extends ResponseBaseTestBase
{
    protected $className = PaymentResponse::class;

    public function casesSetState(): array
    {
        $paymentResponse = new PaymentResponse();
        $paymentResponse->timeout = 'test-timeout';
        $paymentResponse->currency = 'test-currency';
        $paymentResponse->salt = 'test-salt';
        $paymentResponse->orderRef = 'test-order-ref';
        $paymentResponse->merchant = 'test-merchant';
        $paymentResponse->transactionId = 42;
        $paymentResponse->errorCodes = ['test-error'];
        $paymentResponse->paymentUrl = 'test-payment-url';
        $paymentResponse->total = 44.5;

        return [
            'empty' => [
                new PaymentResponse(),
                [
                    'timeout'       => '',
                    'currency'      => '',
                    'salt'          => '',
                    'orderRef'      => '',
                    'merchant'      => '',
                    'transactionId' => 0,
                    'errorCodes'    => [],
                    'paymentUrl'    => '',
                    'total'         => 0.0,
                ],
            ],
            'basic' => [
                $paymentResponse,
                [
                    'timeout'       => 'test-timeout',
                    'currency'      => 'test-currency',
                    'salt'          => 'test-salt',
                    'orderRef'      => 'test-order-ref',
                    'merchant'      => 'test-merchant',
                    'transactionId' => 42,
                    'errorCodes'    => ['test-error'],
                    'paymentUrl'    => 'test-payment-url',
                    'total'         => 44.5,
                ]
            ],
        ];
    }
}
