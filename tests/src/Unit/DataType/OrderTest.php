<?php

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function casesExportData()
    {
        $o1 = new Order();
        $o1->paymentId = 'Foo';
        $o1->currency = 'EUR';
        $o1->orderDate = '2000-01-01 00:00:00';
        $o1->shippingPrice = 999.9;

        return [
            'empty' => [[], new Order()],
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
                    [
                        'ORDER_SHIPPING' => 999.9,
                    ],
                ],
                $o1,
            ]
        ];
    }

    /**
     * @dataProvider casesExportData
     */
    public function testExportData($expected, Order $order)
    {
        static::assertSame($expected, $order->exportData());
    }
}
