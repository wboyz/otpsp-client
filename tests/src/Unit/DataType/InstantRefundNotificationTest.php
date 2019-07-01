<?php

declare(strict_types = 1);

namespace Cheppers\OtpClient\Tests\Unit\DataType;

use Cheppers\OtpClient\DataType\InstantRefundNotification;

/**
 * @covers \Cheppers\OtpClient\DataType\InstantOrderStatus<extended>
 */
class InstantRefundNotificationTest extends TestBase
{
    /**
     * {@inheritdoc}
     */
    protected $className = InstantRefundNotification::class;

    public function casesSetState(): array
    {
        return [
            'basic' => [
                [
                    'orderRef' => 'a',
                    'statusCode' => 'b',
                    'statusName' => 'c',
                    'irnDate' => 'd',
                ],
                [
                    'ORDER_REF' => 'a',
                    'STATUS_CODE' => 'b',
                    'STATUS_NAME' => 'c',
                    'IRN_DATE' => 'd',
                ],
            ],
        ];
    }
}
