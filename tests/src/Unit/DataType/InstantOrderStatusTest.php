<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\InstantOrderStatus;

/**
 * @covers \Cheppers\OtpspClient\DataType\InstantOrderStatus<extended>
 */
class InstantOrderStatusTest extends TestBase
{
    /**
     * {@inheritdoc}
     */
    protected $className = InstantOrderStatus::class;

    public function casesSetState(): array
    {
        return [
            'basic' => [
                [
                    'orderDate' => 'a',
                    'refNo' => 'b',
                    'refNoExt' => 'c',
                    'orderStatus' => 'd',
                    'payMethod' => 'e',
                ],
                [
                    'ORDER_DATE' => 'a',
                    'REFNO' => 'b',
                    'REFNOEXT' => 'c',
                    'ORDER_STATUS' => 'd',
                    'PAYMETHOD' => 'e',
                ],
            ],
        ];
    }
}
