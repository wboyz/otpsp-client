<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\Order;
use Cheppers\OtpspClient\DataType\Product;
use Cheppers\OtpspClient\DataType\Redirect;

/**
 * @covers \Cheppers\OtpspClient\DataType\Redirect<extended>
 */
class RedirectTest extends RedirectBaseTestBase
{

    /**
     * {@inheritdoc}
     */
    protected $className = Redirect::class;

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
                        'MERCHANT' => 'PUBLICTESTHUF',
                    ],
                    [
                        'ORDER_REF' => 'Foo',
                    ],
                    [
                        'ORDER_DATE' => '2000-01-01 00:00:00',
                    ],
                    [
                        'ORDER_PNAME[]' => 'p1-name',
                    ],
                    [
                        'ORDER_PCODE[]' => 'p1-sku',
                    ],
                    [
                        'ORDER_PINFO[]' => 'p1-desc',
                    ],
                    [
                        'ORDER_PRICE[]' => '42',
                    ],
                    [
                        'ORDER_QTY[]' => '1',
                    ],
                    [
                        'ORDER_VAT[]' => '2',
                    ],
                    [
                        'ORDER_PNAME[]' => 'p2-name',
                    ],
                    [
                        'ORDER_PCODE[]' => 'p2-sku',
                    ],
                    [
                        'ORDER_PINFO[]' => 'p2-desc',
                    ],
                    [
                        'ORDER_PRICE[]' => '43',
                    ],
                    [
                        'ORDER_QTY[]' => '3',
                    ],
                    [
                        'DISCOUNT[]' => '4',
                    ],
                    [
                        'ORDER_VAT[]' => '5',
                    ],
                    [
                        'ORDER_SHIPPING' => 9.9,
                    ],
                    [
                        'PRICES_CURRENCY' => 'EUR',
                    ],
                    [
                        'TIMEOUT_URL' => 'http://timeout.exmaple.com',
                    ],
                    [
                        'BACK_REF' => 'http://backref.exmaple.com',
                    ],
                    [
                        'BILL_EMAIL' => 'example@example.com',
                    ],
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
                    [
                        'LANGUAGE' => 'HU',
                    ],
                ],
                [
                    'merchantId' => 'PUBLICTESTHUF',
                    'customerEmail' => 'example@example.com',
                    'products' => [
                        Product::__set_state([
                            'productName' => 'p1-name',
                            'sku' => 'p1-sku',
                            'description' => 'p1-desc',
                            'price' => '42',
                            'quantity' => '1',
                            'discount' => '0',
                            'vat' => '2',
                        ]),
                        [
                            'productName' => 'p2-name',
                            'sku' => 'p2-sku',
                            'description' => 'p2-desc',
                            'price' => '43',
                            'quantity' => '3',
                            'discount' => '4',
                            'vat' => '5',
                        ],
                    ],
                    'langCode' => 'hu',
                    'backrefUrl' => 'http://backref.exmaple.com',
                    'timeoutUrl' => 'http://timeout.exmaple.com',
                    'order' => [
                        'paymentId' => 'Foo',
                        'orderDate' => '2000-01-01 00:00:00',
                        'currency' => 'EUR',
                        'shippingPrice' => 9.9
                    ],
                    'shippingAddress' => [
                        'firstName' => 'Foo',
                        'lastName' => 'Bar',
                        'countryCode' => 'HU',
                        'city' => 'City',
                        'addressLine' => 'Street 1',
                        'addressLine2' => 'Street 2',
                        'postalCode' => '1234',
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function casesGetHashValues()
    {
        return [
            'empty' => [[], []],
            'basic' => [
                [
                    'my merchant ID',
                    'my order ref',
                    '2000-01-01 00:00:00',
                    'p1 name',
                    'p2 name',
                    'p1 code',
                    'p2 code',
                    'p1 info',
                    'p2 info',
                    1,
                    5,
                    2,
                    6,
                    3,
                    7,
                    4,
                    8,
                    'EUR',
                    4.2,
                ],
                [
                    [
                        'MERCHANT' => 'my merchant ID',
                    ],
                    [
                        'ORDER_REF' => 'my order ref',
                    ],
                    [
                        'ORDER_DATE' => '2000-01-01 00:00:00',
                    ],
                    [
                        'PRICES_CURRENCY' => 'EUR',
                    ],
                    [
                        'ORDER_SHIPPING' => 4.2,
                    ],
                    [
                        'ORDER_PNAME[]' => 'p1 name',
                    ],
                    [
                        'ORDER_PCODE[]' => 'p1 code',
                    ],
                    [
                        'ORDER_PINFO[]' => 'p1 info',
                    ],
                    [
                        'ORDER_PRICE[]' => 1,
                    ],
                    [
                        'ORDER_QTY[]' => 2,
                    ],
                    [
                        'DISCOUNT[]' => 3,
                    ],
                    [
                        'ORDER_VAT[]' => 4,
                    ],
                    [
                        'ORDER_PNAME[]' => 'p2 name',
                    ],
                    [
                        'ORDER_PCODE[]' => 'p2 code',
                    ],
                    [
                        'ORDER_PINFO[]' => 'p2 info',
                    ],
                    [
                        'ORDER_PRICE[]' => 5,
                    ],
                    [
                        'ORDER_QTY[]' => 6,
                    ],
                    [
                        'DISCOUNT[]' => 7,
                    ],
                    [
                        'ORDER_VAT[]' => 8,
                    ],
                    [
                        'BILL_EMAIL' => 'example@example.com',
                    ],
                    [
                        'DELIVERY_FNAME' => 'Shipping Fname',
                    ],
                    [
                        'DELIVERY_LNAME' => 'Shipping Lname',
                    ],
                    [
                        'DELIVERY_COUNTRYCODE' => 'Shipping HU',
                    ],
                    [
                        'DELIVERY_CITY' => 'Shipping City',
                    ],
                    [
                        'DELIVERY_ADDRESS' => 'Shipping Street 1',
                    ],
                    [
                        'DELIVERY_ADDRESS2' => 'Shipping Street 2',
                    ],
                    [
                        'DELIVERY_ZIPCODE' => '1234',
                    ],
                    [
                        'BILL_FNAME' => 'Billing Fname',
                    ],
                    [
                        'BILL_LNAME' => 'Billing Lname',
                    ],
                    [
                        'BILL_COMPANY' => 'Billing Company',
                    ],
                    [
                        'BILL_COUNTRYCODE' => 'Billing GB',
                    ],
                    [
                        'BILL_CITY' => 'Billing City',
                    ],
                    [
                        'BILL_ADDRESS' => 'Billing Street 1',
                    ],
                    [
                        'BILL_ADDRESS2' => 'Billing Street 2',
                    ],
                    [
                        'BILL_ZIPCODE' => '4567',
                    ],
                ],
            ]
        ];
    }

    /**
     * @dataProvider casesGetHashValues
     */
    public function testGetHashValues($expected, array $data)
    {
        $redirect = new Redirect();

        static::assertSame($expected, $redirect->getHashValues($data));
    }
}
