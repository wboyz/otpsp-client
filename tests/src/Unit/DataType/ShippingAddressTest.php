<?php

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\ShippingAddress;

/**
 * @covers \Cheppers\OtpspClient\DataType\ShippingAddress<extended>
 */
class ShippingAddressTest extends RedirectBaseTestBase
{
    /**
     * {@inheritdoc}
     */
    protected $className = ShippingAddress::class;

    /**
     * {@inheritdoc}
     */
    public function casesExportData()
    {
        return [
            'empty' => [[], []],
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
                [
                    'firstName' => 'Foo',
                    'lastName' => 'Bar',
                    'countryCode' => 'HU',
                    'city' => 'City',
                    'addressLine' => 'Street 1',
                    'addressLine2' => 'Street 2',
                    'postalCode' => '1234',
                ],
            ]
        ];
    }
}
