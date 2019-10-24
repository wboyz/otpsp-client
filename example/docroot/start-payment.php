<?php

declare(strict_types = 1);

use Cheppers\OtpspClient\Checksum;
use Cheppers\OtpspClient\DataType\Item;
use Cheppers\OtpspClient\DataType\PaymentRequest;
use Cheppers\OtpspClient\OtpSimplePayClient;
use Cheppers\OtpspClient\OtpSimplePayClientInterface;
use GuzzleHttp\Client;
use Psr\Log\Test\TestLogger;

require_once '../../vendor/autoload.php';
require_once '../App.php';

$app = new App();

$now = new DateTime('now');
$guzzle = new Client();
$serializer = new Checksum();
$logger = new TestLogger();
$otpSimple = new OtpSimplePayClient($guzzle, $serializer, $logger);
$otpSimple->setSecretKey($app->getSecretKey());
$timeout = new DateInterval('PT5M');

$paymentRequest = new PaymentRequest();
$paymentRequest->merchant = $app->getMerchantId();
$paymentRequest->orderRef = 'my-order-id-' . $now->format('Y-m-d-h-i-s');
$paymentRequest->customer = 'test-customer';
$paymentRequest->customerEmail = 'test-email@example.com';
$paymentRequest->language = 'HU';
$paymentRequest->currency = 'HUF';
$paymentRequest->salt = 'd471d2fb24c5a395563ff60f8ba769d1';
$paymentRequest->methods = ['CARD'];
$paymentRequest->invoice->name = 'InvoiceName';
$paymentRequest->invoice->company = 'InvoiceCompany';
$paymentRequest->invoice->country = 'hu';
$paymentRequest->invoice->state = 'InvoiceState';
$paymentRequest->invoice->city = 'InvoiceCity';
$paymentRequest->invoice->zip = '1111';
$paymentRequest->invoice->address = 'Address1';
$paymentRequest->invoice->address2 = 'Address2';
$paymentRequest->invoice->phone = '06123456789';
$paymentRequest->delivery->name = 'DeliveryName';
$paymentRequest->delivery->company = 'DeliveryCompany';
$paymentRequest->delivery->country = 'hu';
$paymentRequest->delivery->state = 'DeliveryState';
$paymentRequest->delivery->city = 'DeliveryCity';
$paymentRequest->delivery->zip = '2222';
$paymentRequest->delivery->address = 'DeliveryAddress1';
$paymentRequest->delivery->address2 = 'DeliveryAddress2';
$paymentRequest->delivery->phone = '06198765432';
$paymentRequest->timeout = $now->add($timeout)->format(OtpSimplePayClientInterface::DATETIME_FORMAT);
$paymentRequest->urls->success = $app->getBaseUrl() . '/return.php';
$paymentRequest->urls->cancel = $app->getBaseUrl() . '/return.php';
$paymentRequest->urls->timeout = $app->getBaseUrl() . '/return.php';
$paymentRequest->urls->fail = $app->getBaseUrl() . '/return.php';
$paymentRequest->shippingCost = 100;
$paymentRequest->discount = 50;
$paymentRequest->items['a'] = Item::__set_state([
    'ref' => 'sku-product-1',
    'title' => 'Product 1',
    'description' => 'Description 1',
    'amount' => 1,
    'price' => 500,
    'tax' => 27,
]);
$paymentRequest->items['b'] = Item::__set_state([
    'ref' => 'sku-product-2',
    'title' => 'Product 2',
    'description' => 'Description 2',
    'amount' => 2,
    'price' => 600,
    'tax' => 27,
]);

$paymentRequest->total = 0;
$paymentRequest->total += $paymentRequest->shippingCost;
$paymentRequest->total -= $paymentRequest->discount;
foreach ($paymentRequest->items as $item) {
    $paymentRequest->total += ($item->price + ($item->price / 100 * $item->tax)) * $item->amount;
}

$startPaymentResponse = $otpSimple->startPayment($paymentRequest);

// In a real application do not print anything,
// just redirect the client to $startPaymentResponse->paymentURL.
echo $app
    ->twig()
    ->render(
        'start-payment.html.twig',
        [
            'paymentRequest' => $paymentRequest,
            'paymentRequestJson' => $app->jsonEncode($paymentRequest),
            'startPaymentResponse' => $startPaymentResponse,
            'startPaymentResponseJson' => $app->jsonEncode($startPaymentResponse),
            'logEntriesJson' => $app->jsonEncode($logger->records),
        ]
    );
