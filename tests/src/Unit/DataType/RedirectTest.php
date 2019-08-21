<?php

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\Redirect;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Cheppers\OtpspClient\DataType\Redirect<extended>
 */
class RedirectTest extends RedirectBaseTestBase
{
    /**
     * @var string|\Cheppers\OtpspClient\DataType\RedirectBase
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
                    'merchant' => 'test-merchant',
                    'orderRef' => 'test-order-ref',
                    'customer' => 'test-customer',
                    'customerEmail' => 'test-email@example.com',
                    'language' => 'HU',
                    'currency' => 'HUF',
                    'total' => 100,
                    'salt' => 'd471d2fb24c5a395563ff60f8ba769d1',
                    'methods' => ['CARD'],
                    'invoice' => [
                        'name' => 'InvoiceName',
                        'company' => 'InvoiceCompany',
                        'country' => 'hu',
                        'state' => 'InvoiceState',
                        'city' => 'InvoiceCity',
                        'zip' => '1111',
                        'address' => 'Address1',
                        'address2' => 'Address2',
                        'phone' => '06123456789',
                    ],
                    'delivery' => [
                        'name' => 'DeliveryName',
                        'company' => 'DeliveryCompany',
                        'country' => 'hu',
                        'state' => 'DeliveryState',
                        'city' => 'DeliveryCity',
                        'zip' => '2222',
                        'address' => 'DeliveryAddress1',
                        'address2' => 'DeliveryAddress2',
                        'phone' => '06198765432',
                    ],
                    'items' => [
                        [
                            'ref' => 'product-1-ref',
                            'title' => 'Product1',
                            'description' => 'Product1Description',
                            'amount' => 1,
                            'price' => 1499,
                            'tax' => 42,
                        ],
                        [
                            'ref' => 'product-2-ref',
                            'title' => 'Product2',
                            'description' => 'Product2Description',
                            'amount' => 2,
                            'price' => 2499,
                            'tax' => 43,
                        ]
                    ],
                    'shippingCost' => 20,
                    'discount' => 41,
                    'timeout' => '2018-09-07T20:51:13+00:00',
                    'url' => 'test-url.com',
                    'urls' => [
                        'success' => 'success-test.com',
                        'fail' => 'fail-test.com',
                        'cancel' => 'cancel-test.com',
                        'timeout' => 'timeout-test.com',
                    ],
                    'sdkVersion' => 'SimplePay_PHP_SDK_2.0_180930:33ccd5ed8e8a965d18abfae333404184',
                ],
                [
                    'merchant' => 'test-merchant',
                    'orderRef' => 'test-order-ref',
                    'customer' => 'test-customer',
                    'customerEmail' => 'test-email@example.com',
                    'language' => 'HU',
                    'currency' => 'HUF',
                    'total' => 100,
                    'salt' => 'd471d2fb24c5a395563ff60f8ba769d1',
                    'methods' => ['CARD'],
                    'invoice' => [
                        'name' => 'InvoiceName',
                        'company' => 'InvoiceCompany',
                        'country' => 'hu',
                        'state' => 'InvoiceState',
                        'city' => 'InvoiceCity',
                        'zip' => '1111',
                        'address' => 'Address1',
                        'address2' => 'Address2',
                        'phone' => '06123456789',
                    ],
                    'delivery' => [
                        'name' => 'DeliveryName',
                        'company' => 'DeliveryCompany',
                        'country' => 'hu',
                        'state' => 'DeliveryState',
                        'city' => 'DeliveryCity',
                        'zip' => '2222',
                        'address' => 'DeliveryAddress1',
                        'address2' => 'DeliveryAddress2',
                        'phone' => '06198765432',
                    ],
                    'items' => [
                        [
                            'ref' => 'product-1-ref',
                            'title' => 'Product1',
                            'description' => 'Product1Description',
                            'amount' => 1,
                            'price' => 1499,
                            'tax' => 42,
                        ],
                        [
                            'ref' => 'product-2-ref',
                            'title' => 'Product2',
                            'description' => 'Product2Description',
                            'amount' => 2,
                            'price' => 2499,
                            'tax' => 43,
                        ]
                    ],
                    'shippingCost' => 20,
                    'discount' => 41,
                    'timeout' => '2018-09-07T20:51:13+00:00',
                    'url' => 'test-url.com',
                    'urls' => [
                        'success' => 'success-test.com',
                        'fail' => 'fail-test.com',
                        'cancel' => 'cancel-test.com',
                        'timeout' => 'timeout-test.com',
                    ],
                ],
            ],
        ];
    }

    public function casesExportJsonString()
    {
        return [
            'empty' => ['', []],
            'basic' => [
                '{
                    "merchant":"test-merchant",
                    "orderRef":"test-order-ref",
                    "customer":"test-customer",
                    "customerEmail":"test-email@example.com",
                    "language":"HU",
                    "currency": "HUF",
                    "total":100,
                    "salt":"d471d2fb24c5a395563ff60f8ba769d1",
                    "methods":[
                        "CARD"
                    ],
                    "invoice":{
                        "name":"InvoiceName",
                        "company":"InvoiceCompany",
                        "country":"hu",
                        "state":"InvoiceState",
                        "city":"InvoiceCity",
                        "zip":"1111",
                        "address":"Address1",
                        "address2":"Address2",
                        "phone":"06123456789"
                    },
                    "delivery":{
                        "name":"DeliveryName",
                        "company":"DeliveryCompany",
                        "country":"hu",
                        "state":"DeliveryState",
                        "city":"DeliveryCity",
                        "zip":"2222",
                        "address":"DeliveryAddress1",
                        "address2":"DeliveryAddress2",
                        "phone":"06198765432"
                    },
                    "items":[
                        {
                            "ref":"product-1-ref",
                            "title":"Product1",
                            "description":"Product1Description",
                            "amount":1,
                            "price":1499,
                            "tax":42
                        },
                        {
                            "ref":"product-2-ref",
                            "title":"Product2",
                            "description":"Product2Description",
                            "amount":2,
                            "price":2499,
                            "tax":43
                        }
                    ],
                    "shippingCost":20,
                    "discount":41,
                    "timeout":"2018-09-07T20:51:13+00:00",
                    "url":"test-url.com",
                    "urls":{
                        "success":"success-test.com",
                        "fail":"fail-test.com",
                        "cancel":"cancel-test.com",
                        "timeout":"timeout-test.com"
                    },
                    "sdkVersion":"SimplePay_PHP_SDK_2.0_180930:33ccd5ed8e8a965d18abfae333404184"
                }',
                [
                    'merchant' => 'test-merchant',
                    'orderRef' => 'test-order-ref',
                    'customer' => 'test-customer',
                    'customerEmail' => 'test-email@example.com',
                    'language' => 'HU',
                    'currency' => 'HUF',
                    'total' => 100,
                    'salt' => 'd471d2fb24c5a395563ff60f8ba769d1',
                    'methods' => ['CARD'],
                    'invoice' => [
                        'name' => 'InvoiceName',
                        'company' => 'InvoiceCompany',
                        'country' => 'hu',
                        'state' => 'InvoiceState',
                        'city' => 'InvoiceCity',
                        'zip' => '1111',
                        'address' => 'Address1',
                        'address2' => 'Address2',
                        'phone' => '06123456789',
                    ],
                    'delivery' => [
                        'name' => 'DeliveryName',
                        'company' => 'DeliveryCompany',
                        'country' => 'hu',
                        'state' => 'DeliveryState',
                        'city' => 'DeliveryCity',
                        'zip' => '2222',
                        'address' => 'DeliveryAddress1',
                        'address2' => 'DeliveryAddress2',
                        'phone' => '06198765432',
                    ],
                    'items' => [
                        [
                            'ref' => 'product-1-ref',
                            'title' => 'Product1',
                            'description' => 'Product1Description',
                            'amount' => 1,
                            'price' => 1499,
                            'tax' => 42,
                        ],
                        [
                            'ref' => 'product-2-ref',
                            'title' => 'Product2',
                            'description' => 'Product2Description',
                            'amount' => 2,
                            'price' => 2499,
                            'tax' => 43,
                        ]
                    ],
                    'shippingCost' => 20,
                    'discount' => 41,
                    'urls' => [
                        'success' => 'success-test.com',
                        'fail' => 'fail-test.com',
                        'cancel' => 'cancel-test.com',
                        'timeout' => 'timeout-test.com',
                    ],
                    'timeout' => '2018-09-07T20:51:13+00:00',
                    'url' => 'test-url.com',
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesExportJsonString
     */
    public function testExportJsonString(string $expected, array $values)
    {
        $data = Redirect::__set_state($values);
        $string = trim(preg_replace('/\s+/', '', $expected));
        static::assertSame($string, $data->exportJsonString());
    }
}
