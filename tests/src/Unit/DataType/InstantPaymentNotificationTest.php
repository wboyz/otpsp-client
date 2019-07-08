<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\InstantPaymentNotification;

/**
 * @covers \Cheppers\OtpspClient\DataType\InstantPaymentNotification<extended>
 */
class InstantPaymentNotificationTest extends TestBase
{
    /**
     * {@inheritdoc}
     */
    protected $className = InstantPaymentNotification::class;

    public function casesSetState(): array
    {
        return [
            'basic' => [
                [
                    'refNoExt' => 'a',
                    'refNo' => 'b',
                    'orderStatus' => 'c',
                    'ipnPId' => 'd',
                    'ipnPName' => 'e',
                    'ipnDate' => 'f',
                    'hash' => 'g',
                ],
                [
                    'REFNOEXT' => 'a',
                    'REFNO' => 'b',
                    'ORDER_STATUS' => 'c',
                    'IPN_PID' => 'd',
                    'IPN_PNAME' => 'e',
                    'IPN_DATE' => 'f',
                    'HASH' => 'g',
                ],
            ],
        ];
    }
}
