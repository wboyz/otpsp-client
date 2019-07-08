<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\InstantDeliveryNotification;

/**
 * @covers \Cheppers\OtpspClient\DataType\InstantDeliveryNotification<extended>
 */
class InstantDeliveryNotificationTest extends TestBase
{
    /**
     * {@inheritdoc}
     */
    protected $className = InstantDeliveryNotification::class;

    /**
     * {@inheritdoc}
     */
    public function casesSetState(): array
    {
        return [
            'basic' => [
                [
                    'orderRef' => 'a',
                    'statusCode' => 10,
                    'statusName' => 'c',
                    'idnDate' => 'd',
                ],
                [
                    'ORDER_REF' => 'a',
                    'STATUS_CODE' => '10',
                    'STATUS_NAME' => 'c',
                    'IDN_DATE' => 'd',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function casesExportForChecksum(): array {
        return [];
    }
}
