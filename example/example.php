<?php

use GuzzleHttp\Psr7\Request;

require_once 'vendor/autoload.php';


// Create instance from OtpSimplePayClient and set secret key
$guzzle = new \GuzzleHttp\Client();
$serializer = new \Cheppers\OtpspClient\Checksum();
$logger = new \Psr\Log\Test\TestLogger();
$datetime = new DateTime();
$client = new \Cheppers\OtpspClient\OtpSimplePayClient($guzzle, $serializer, $logger, $datetime);

$client->setSecretKey('');


// Starting payment
$valuesPayment = [
    'merchant' => '',
    'orderRef' => 'order-id-1',
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
    'timeout' => '2019-09-07T20:51:13+00:00',
    'url' => 'http://81b077c9.ngrok.io/en/dummy-controller',
];

$client->startPayment(\Cheppers\OtpspClient\DataType\PaymentRequest::__set_state($valuesPayment));


// Get IPN request from SimplePay, you should parse and reply.
// Example request from SimplePay
$request = new Request(
    'POST',
    'test-uri.com',
    [
        'Content-Type' => 'application/json',
        'Signature' => 'jRLcA9EYhm+xjfyXCJ9ft/OUuhgtRR5Ct2IQYCXAlTGtubvn7kBsBmp/5K2Ex',
    ],
    json_encode([
        'salt' => 'test-salt',
        'orderRef' => 'test-orderRef',
        'method' => 'test-card',
        'merchant' => 'test-merchant',
        'finishDate' => 'test-finishDate',
        'paymentDate' => 'test-paymentDate',
        'transactionId' => 42,
        'status' => 'test-status',
    ]));

$ipn = $client->parseInstantPaymentNotificationRequest($request);

// Success response for Instant Payment Notification
$client->getInstantPaymentNotificationSuccessResponse($ipn);
