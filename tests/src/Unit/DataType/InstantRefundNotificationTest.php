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
    public function casesExportForChecksum(): array {
        return [];
    }
}
