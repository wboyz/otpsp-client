<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\InstantDeliveryNotification;

/**
 * @covers \Cheppers\OtpspClient\DataType\InstantOrderStatus<extended>
 */
class InstantDeliveryNotificationTest extends TestBase
{
    /**
     * {@inheritdoc}
     */
    protected $className = InstantDeliveryNotification::class;

    public function casesSetState(): array
    {
        return [
            'basic' => [
                [
                    'orderRef' => 'a',
                    'statusCode' => 'b',
                    'statusName' => 'c',
                    'idnDate' => 'd',
                ],
                [
                    'ORDER_REF' => 'a',
                    'STATUS_CODE' => 'b',
                    'STATUS_NAME' => 'c',
                    'IDN_DATE' => 'd',
                ],
            ],
        ];
    }
}
