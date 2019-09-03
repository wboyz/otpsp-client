<?php

use Cheppers\OtpspClient\Checksum;
use Cheppers\OtpspClient\DataType\PaymentRequest;
use Cheppers\OtpspClient\OtpSimplePayClient;
use GuzzleHttp\Psr7\Request;
use Psr\Log\Test\TestLogger;

require_once 'vendor/autoload.php';

$secretKey = getenv('SECRET_KEY');
$merchantId = getenv('MERCHANT_ID');

$guzzle = new \GuzzleHttp\Client();
$serializer = new Checksum();
$logger = new TestLogger();
$otpSimple = new OtpSimplePayClient($guzzle, $serializer, $logger);
$otpSimple->setSecretKey($secretKey);

$now = new \DateTime('now');

$valuesPayment = [
    'merchant' => $merchantId,
    'orderRef' => 'order-id-' . $now->format('Y-m-d-h-i-s'),
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

$response = $otpSimple->startPayment(PaymentRequest::__set_state($valuesPayment));
var_dump($response);

// Get IPN request from SimplePay, you should parse and reply.
// Example request from SimplePay
$body = json_encode([
    'salt' => 'test-salt',
    'orderRef' => $valuesPayment['orderRef'],
    'method' => 'test-card',
    'merchant' => $merchantId,
    'finishDate' => 'test-finishDate',
    'paymentDate' => 'test-paymentDate',
    'transactionId' => 42,
    'status' => 'test-status',
]);
$request = new Request(
    'POST',
    'test-uri.com',
    [
        'Content-Type' => 'application/json',
        'Signature' => $serializer->calculate($secretKey, $body),
    ],
    $body
);

$ipn = $otpSimple->parseInstantPaymentNotificationRequest($request);
var_dump($ipn);

// Success response for Instant Payment Notification.
$responseToSendBackToOtp = $otpSimple->getInstantPaymentNotificationSuccessResponse($ipn);
var_dump($responseToSendBackToOtp);
