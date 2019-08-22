<?php

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\Address;
use Cheppers\OtpspClient\DataType\Item;
use Cheppers\OtpspClient\DataType\PaymentRequest;
use Cheppers\OtpspClient\DataType\Urls;

/**
 * @covers \Cheppers\OtpspClient\DataType\PaymentRequest<extended>
 */
class PaymentRequestTest extends RequestBaseTestBase
{
    /**
     * @var string|\Cheppers\OtpspClient\DataType\RequestBase
     */
    protected $className = PaymentRequest::class;

    /**
     * {@inheritdoc}
     */
    public function casesSetState()
    {
        return [
            'empty' => [new PaymentRequest(), []],
            'basic' => [
                $this->getBasePaymentRequest(),
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

    public function casesJsonSerialize()
    {
        return [
            'empty' => [
                [
                    'merchant' => '',
                    'orderRef' => '',
                    'customer' => '',
                    'customerEmail' => '',
                    'language' => '',
                    'currency' => '',
                    'total' => '',
                    'salt' => '',
                    'methods' => ['CARD'],
                    'invoice' => [
                        'name' => '',
                        'country' => '',
                        'state' => '',
                        'city' => '',
                        'zip' => '',
                        'address' => '',
                        'address2' => '',
                    ],
                    'delivery' => [
                        'name' => '',
                        'country' => '',
                        'state' => '',
                        'city' => '',
                        'zip' => '',
                        'address' => '',
                        'address2' => '',
                    ],
                    'shippingCost' => 0,
                    'discount' => 0,
                    'timeout' => '',
                    'url' => '',
                    'urls' => [],
                    'sdkVersion' => 'SimplePay_PHP_SDK_2.0_180930:33ccd5ed8e8a965d18abfae333404184',
                ],
                new PaymentRequest(),
            ],
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
                $this->getBasePaymentRequest(),
            ],
        ];
    }

    protected function getBasePaymentRequest(): PaymentRequest
    {
        $expectedPaymentRequest = new PaymentRequest();
        $invoice = new Address();
        $delivery = new Address();
        $itemOne = new Item();
        $itemTwo = new Item();
        $urls = new Urls();
        $expectedPaymentRequest->merchant = 'test-merchant';
        $expectedPaymentRequest->orderRef = 'test-order-ref';
        $expectedPaymentRequest->customer = 'test-customer';
        $expectedPaymentRequest->customerEmail = 'test-email@example.com';
        $expectedPaymentRequest->language = 'HU';
        $expectedPaymentRequest->currency = 'HUF';
        $expectedPaymentRequest->total = 100;
        $expectedPaymentRequest->salt = 'd471d2fb24c5a395563ff60f8ba769d1';
        $expectedPaymentRequest->methods = ['CARD'];
        $expectedPaymentRequest->shippingCost = 20;
        $expectedPaymentRequest->discount = 41;
        $expectedPaymentRequest->timeout = '2018-09-07T20:51:13+00:00';
        $expectedPaymentRequest->url = 'test-url.com';
        $invoice->name = 'InvoiceName';
        $invoice->company = 'InvoiceCompany';
        $invoice->country = 'hu';
        $invoice->state = 'InvoiceState';
        $invoice->city = 'InvoiceCity';
        $invoice->zip = '1111';
        $invoice->address = 'Address1';
        $invoice->address2 = 'Address2';
        $invoice->phone = '06123456789';
        $delivery->name = 'DeliveryName';
        $delivery->company = 'DeliveryCompany';
        $delivery->country = 'hu';
        $delivery->state = 'DeliveryState';
        $delivery->city = 'DeliveryCity';
        $delivery->zip = '2222';
        $delivery->address = 'DeliveryAddress1';
        $delivery->address2 = 'DeliveryAddress2';
        $delivery->phone = '06198765432';
        $itemOne->ref = 'product-1-ref';
        $itemOne->title = 'Product1';
        $itemOne->description = 'Product1Description';
        $itemOne->amount = 1;
        $itemOne->price = 1499;
        $itemOne->tax = 42;
        $itemTwo->ref = 'product-2-ref';
        $itemTwo->title = 'Product2';
        $itemTwo->description = 'Product2Description';
        $itemTwo->amount = 2;
        $itemTwo->price = 2499;
        $itemTwo->tax = 43;
        $urls->success = 'success-test.com';
        $urls->fail = 'fail-test.com';
        $urls->cancel = 'cancel-test.com';
        $urls->timeout = 'timeout-test.com';
        $expectedPaymentRequest->urls = $urls;
        $expectedPaymentRequest->invoice = $invoice;
        $expectedPaymentRequest->items = [$itemOne, $itemTwo];
        $expectedPaymentRequest->delivery = $delivery;

        return $expectedPaymentRequest;
    }
}
