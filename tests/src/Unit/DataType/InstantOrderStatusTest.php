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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function casesExportForChecksum(): array
    {
        return [
            'valid' => [
                [
                    '2016-04-25 13:35:07',
                    '99016764',
                    '101010514615913074586',
                    'COMPLETE',
                    'Visa/MasterCard',
                ],
                [
                    'ORDER_DATE' => '2016-04-25 13:35:07',
                    'REFNO' => '99016764',
                    'REFNOEXT' => '101010514615913074586',
                    'ORDER_STATUS' => 'COMPLETE',
                    'PAYMETHOD' => 'Visa/MasterCard',
                    'HASH' => '76621655113b7fd00d605075d28023f1',
                ],
            ],
        ];
    }
}
