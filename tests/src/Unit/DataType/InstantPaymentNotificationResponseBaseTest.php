<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\InstantPaymentNotification;

/**
 * @covers \Cheppers\OtpspClient\DataType\InstantPaymentNotification<extended>
 */
class InstantPaymentNotificationResponseBaseTest extends ResponseBaseTestBase
{
    /**
     * {@inheritdoc}
     */
    protected $className = InstantPaymentNotification::class;

    /**
     * {@inheritdoc}
     */
    public function casesSetState(): array
    {
        return [
            'basic' => [
                [
                    'refNoExt' => 'a',
                    'refNo' => 'b',
                    'ipnOrderStatus' => 'c',
                    'ipnPId' => 'd',
                    'ipnPName' => 'e',
                    'ipnDate' => 'f',
                    'hash' => 'g',
                ],
                [
                    'REFNOEXT' => 'a',
                    'REFNO' => 'b',
                    'ORDERSTATUS' => 'c',
                    'IPN_PID' => 'd',
                    'IPN_PNAME' => 'e',
                    'IPN_DATE' => 'f',
                    'HASH' => 'g',
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
                    '1111',
                    '1234',
                    'COMPLETE',
                    '42',
                    'Product_1',
                    '2016040813426',
               ],
               [
                    'REFNO' => '1111',
                    'REFNOEXT' => '1234',
                    'ORDERSTATUS' => 'COMPLETE',
                    'IPN_PID' => [
                        '42',
                    ],
                    'IPN_PNAME' => [
                        'Product_1',
                    ],
                    'IPN_DATE' => '2016040813426',
                    'HASH' => '12345678',
               ],
           ],
        ];
    }
}
