<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\InstantRefundNotification;

/**
 * @covers \Cheppers\OtpspClient\DataType\InstantRefundNotification<extended>
 */
class InstantRefundNotificationTest extends TestBase
{
    /**
     * {@inheritdoc}
     */
    protected $className = InstantRefundNotification::class;

    /**
     * {@inheritdoc}
     */
    public function casesSetState(): array
    {
        return [
            'basic' => [
                [
                    'orderRef' => 'a',
                    'statusCode' => 42,
                    'statusName' => 'c',
                    'irnDate' => 'd',
                ],
                [
                    'ORDER_REF' => 'a',
                    'STATUS_CODE' => '42',
                    'STATUS_NAME' => 'c',
                    'IRN_DATE' => 'd',
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
                    '99017212',
                    1,
                    'OK',
                    '2016-04-29 12:59:57',
                ],
                [
                    'ORDER_REF' => '99017212',
                    'STATUS_CODE' => '1',
                    'STATUS_NAME' => 'OK',
                    'IRN_DATE' => '2016-04-29 12:59:57',
                    'HASH' => '2c071f3bc310ba6a2df2f93095ac2c91',
                ],
            ],
        ];
    }
}
