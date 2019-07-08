<?php

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\ShippingAddress;
use PHPUnit\Framework\TestCase;

class ShippingAddressTest extends TestCase
{
    public function casesExportData()
    {
        $shipping = new ShippingAddress();
        $shipping->firstName = 'Foo';
        $shipping->lastName = 'Bar';
        $shipping->postalCode = '1234';
        $shipping->countryCode = 'HU';
        $shipping->city = 'City';
        $shipping->addressLine = 'Street 1';
        $shipping->addressLine2 = 'Street 2';

        return [
            'empty' => [[], new ShippingAddress()],
            'basic' => [
                [
                    [
                        'DELIVERY_FNAME' => 'Foo',
                    ],
                    [
                        'DELIVERY_LNAME' => 'Bar',
                    ],
                    [
                        'DELIVERY_COUNTRYCODE' => 'HU',
                    ],
                    [
                        'DELIVERY_CITY' => 'City',
                    ],
                    [
                        'DELIVERY_ADDRESS' => 'Street 1',
                    ],
                    [
                        'DELIVERY_ADDRESS2' => 'Street 2',
                    ],
                    [
                        'DELIVERY_ZIPCODE' => '1234',
                    ],
                ],
                $shipping,
            ]
        ];
    }

    /**
     * @dataProvider casesExportData
     */
    public function testExportData($expected, ShippingAddress $shippingAddress)
    {
        self::assertSame($expected, $shippingAddress->exportData());
    }
}
