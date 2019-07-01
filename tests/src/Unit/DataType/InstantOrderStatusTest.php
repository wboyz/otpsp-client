<?php

declare(strict_types = 1);

namespace Cheppers\OtpClient\Tests\Unit\DataType;

use Cheppers\OtpClient\DataType\InstantOrderStatus;

/**
 * @covers \Cheppers\OtpClient\DataType\InstantOrderStatus<extended>
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
