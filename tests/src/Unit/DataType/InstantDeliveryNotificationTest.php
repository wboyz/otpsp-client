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
    public function casesExportForChecksum(): array
    {
        return [
            'valid' => [
                [
                    '99017183',
                    1,
                    'OK',
                    '2016-04-29 11:16:37',
                ],
                [
                    'ORDER_REF' => '99017183',
                    'STATUS_CODE' => '1',
                    'STATUS_NAME' => 'OK',
                    'IDN_DATE' => '2016-04-29 11:16:37',
                    'HASH' => '39d5a164d54b10c707b5a0fd32088cf9'
                ],
            ],
        ];
    }
}
