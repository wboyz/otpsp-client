<?php

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\BillingAddress;

/**
 * @covers \Cheppers\OtpspClient\DataType\BillingAddress<extended>
 */
class BillingAddressTest extends RedirectBaseTestBase
{
    /**
     * {@inheritdoc}
     */
    protected $className = BillingAddress::class;

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
                        'BILL_FNAME' => 'Foo',
                    ],
                    [
                        'BILL_LNAME' => 'Bar',
                    ],
                    [
                        'BILL_COMPANY' => 'My Org 01',
                    ],
                    [
                        'BILL_COUNTRYCODE' => 'HU',
                    ],
                    [
                        'BILL_CITY' => 'City',
                    ],
                    [
                        'BILL_ADDRESS' => 'Street 1',
                    ],
                    [
                        'BILL_ADDRESS2' => 'Street 2',
                    ],
                    [
                        'BILL_ZIPCODE' => '1234',
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
                    'organization' => 'My Org 01',
                ],
            ]
        ];
    }
}
