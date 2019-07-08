<?php

namespace Cheppers\OtpspClient;

use Cheppers\OtpspClient\DataType\BackRef;
use Cheppers\OtpspClient\DataType\Base;
use Cheppers\OtpspClient\DataType\InstantDeliveryNotification;
use Cheppers\OtpspClient\DataType\InstantOrderStatus;
use Cheppers\OtpspClient\DataType\InstantRefundNotification;
use Cheppers\OtpspClient\DataType\InstantPaymentNotification;
use DateTimeInterface;
use GuzzleHttp\ClientInterface;

interface OtpSimplePayClientInterface
{
    const RETURN_CODE_SUCCESS = '000';

    const RETURN_CODE_SUCCESS_1 = '001';

    const STATUS_CODE_SUCCESS = 1;

    const STATUS_CODE_NOT_FOUND = 5011;

    const CONTROL_KEY = 'ctrl';

    public function getBaseUri(): string;

    /**
     * @return $this
     */
    public function setBaseUri(string $baseUri);

    public function getClient(): ClientInterface;

    /**
     * @return $this
     */
    public function setClient(ClientInterface $client);

    public function getDateTime(): DateTimeInterface;

    /**
     * @return $this
     */
    public function setDateTime(DateTimeInterface $dateTime);

    public function getMerchantId(): string;

    /**
     * @return $this
     */
    public function setMerchantId(string $merchantId);

    public function getSecretKey(): string;

    /**
     * @return $this
     */
    public function setSecretKey(string $secretKey);

    /**
     * @return string[]
     */
    public function getSupportedLanguages(): array;

    /**
     * @param string[] $supportedLanguages
     *   Two letter lower-case language codes.
     *
     * @return $this
     */
    public function setSupportedLanguages(array $supportedLanguages);

    public function instantDeliveryNotificationPost(
        string $orderRef,
        string $orderAmount,
        string $orderCurrency
    ): ?InstantDeliveryNotification;

    public function instantRefundNotificationPost(
        string $orderRef,
        string $orderAmount,
        string $orderCurrency,
        string $refundAmount
    ): ?InstantRefundNotification;

    public function instantOrderStatusPost(string $refNoExt): ?InstantOrderStatus;

    public function parseResponseXml(string $xml): array;

    /**
     * @return string[]
     */
    public function parseResponseString(string $xml, string $dateKey): array;

    public function isValidChecksum(string $expectedHash, array $values): bool;

    public function getInstantPaymentNotificationSuccessResponse(InstantPaymentNotification $ipn): array;

    public function getInstantPaymentNotificationFailedResponse(): array;

    /**
     * @return string[]
     */
    public function getSuccessReturnCodes(): array;

    public function isPaymentSuccess(string $returnCode): bool;

    /**
     * @return $this
     */
    public function validateStatusCode(array $values);

    /**
     * @return $this
     */
    public function validateHash(string $hash, array $values);

    /**
     * @return $this
     */
    public function validateResponseStatusCode(int $statusCode);

    public function parseBackRefRequest(string $url): BackRef;

    public function parseInstantPaymentNotificationRequest(string $body): InstantPaymentNotification;
}
