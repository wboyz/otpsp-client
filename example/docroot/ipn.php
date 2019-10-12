<?php

declare(strict_types = 1);

use Cheppers\OtpspClient\Checksum;
use Cheppers\OtpspClient\OtpSimplePayClient;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Log\Test\TestLogger;

require_once '../../vendor/autoload.php';
require_once '../App.php';

$app = new App();

$guzzle = new Client();
$serializer = new Checksum();
$logger = new TestLogger();
$otpSimple = new OtpSimplePayClient($guzzle, $serializer, $logger);
$otpSimple->setSecretKey($app->getSecretKey());

$request = new Request(
    $_SERVER['REQUEST_METHOD'],
    $app->requestUri(),
    getallheaders(),
    fopen('php://input', 'r')
);

$ipn = $otpSimple->parseInstantPaymentNotificationRequest($request);

$app->log('ipn', $app->jsonEncode($ipn));

$responseParts = $otpSimple->getInstantPaymentNotificationSuccessParts($ipn);
http_response_code($responseParts['statusCode']);
foreach ($responseParts['headers'] as $key => $value) {
    header("$key: $value");
}
echo $responseParts['body'];
