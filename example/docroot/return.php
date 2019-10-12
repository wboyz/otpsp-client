<?php

declare(strict_types = 1);

use Cheppers\OtpspClient\Checksum;
use Cheppers\OtpspClient\OtpSimplePayClient;
use GuzzleHttp\Client;
use Psr\Log\Test\TestLogger;

require_once '../../vendor/autoload.php';
require_once '../App.php';

$app = new App();

$guzzle = new Client();
$serializer = new Checksum();
$logger = new TestLogger();
$otpSimple = new OtpSimplePayClient($guzzle, $serializer, $logger);
$otpSimple->setSecretKey($app->getSecretKey());

$backResponse = $otpSimple->parseBackResponse($app->requestUri());

echo $app
    ->twig()
    ->render(
        'return.html.twig',
        [
            'backResponse' => $backResponse,
            'backResponseJson' => $app->jsonEncode($backResponse),
            'logEntriesJson' => $app->jsonEncode($logger->records),
        ]
    );
