<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\Order;

/**
 * @covers \Cheppers\OtpspClient\DataType\Order<extended>
 */
class OrderTest extends RedirectBaseTestBase
{

    /**
     * {@inheritdoc}
     */
    protected $className = Order::class;

    /**
     * {@inheritdoc}
     */
    public function casesExportData()
    {
        return [
            'empty' => [[], []],
            'basic' => [
                [
                    [
                        'ORDER_REF' => 'Foo',
                    ],
                    [
                        'ORDER_DATE' => '2000-01-01 00:00:00',
                    ],
                    [
                        'PRICES_CURRENCY' => 'EUR',
                    ],
                ],
                [
                    'paymentId' => 'Foo',
                    'currency' => 'EUR',
                    'orderDate' => '2000-01-01 00:00:00',
                ],
            ]
        ];
    }
}
