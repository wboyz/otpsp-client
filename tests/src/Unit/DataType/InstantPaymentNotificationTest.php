<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\InstantPaymentNotification;

/**
 * @covers \Cheppers\OtpspClient\DataType\InstantPaymentNotification<extended>
 */
class InstantPaymentNotificationTest extends ResponseBaseTestBase
{

    /**
     * @var string|\Cheppers\OtpspClient\DataType\RequestBase
     */
    protected $className = InstantPaymentNotification::class;

    /**
     * {@inheritdoc}
     */
    public function casesSetState(): array
    {
        return [
            'empty' => [new InstantPaymentNotification(), []],
            'basic' => [
                $this->getBaseIpn(),
                [
                    'cardMask' => 'xxxx-xxxx-xxxx-0425',
                    'salt' => 'test-salt',
                    'orderRef' => 'test-order-ref',
                    'method' => 'CARD',
                    'merchant' => 'test-merchant',
                    'finishDate' => '2019-09-01T00:12:42+02:00',
                    'expiry' => '2021-04-02T00:00:00+02:00',
                    'paymentDate' => '2019-09-02T00:12:42+02:00',
                    'transactionId' => 42,
                    'status' => 'FINISHED',
                    'receiveDate' => '2019-09-03T00:12:42+02:00',
                ],
            ],
            'non existing property' => [
                new InstantPaymentNotification(),
                ['bad_porperty' => 'value'],
            ],
        ];
    }

    public function casesJsonSerialize()
    {
        return [
            'empty' => [
                [
                    'cardMask' => '',
                    'method' => 'CARD',
                    'finishDate' => '',
                    'expiry' => '',
                    'paymentDate' => '',
                    'status' => '',
                    'receiveDate' => '',
                    'salt' => '',
                    'merchant' => '',
                    'orderRef' => '',
                    'transactionId' => 0,
                ],
                new InstantPaymentNotification(),
            ],
            'basic' => [
                [
                    'cardMask' => 'xxxx-xxxx-xxxx-0425',
                    'method' => 'CARD',
                    'finishDate' => '2019-09-01T00:12:42+02:00',
                    'expiry' => '2021-04-02T00:00:00+02:00',
                    'paymentDate' => '2019-09-02T00:12:42+02:00',
                    'status' => 'FINISHED',
                    'receiveDate' => '2019-09-03T00:12:42+02:00',
                    'salt' => 'test-salt',
                    'merchant' => 'test-merchant',
                    'orderRef' => 'test-order-ref',
                    'transactionId' => 42,
                ],
                $this->getBaseIpn(),
            ],
        ];
    }

    /**
     * @dataProvider casesJsonSerialize
     */
    public function testJsonSerialize(
        array $expected,
        InstantPaymentNotification $request
    ) {
        static::assertSame($expected, $request->jsonSerialize());
    }

    protected function getBaseIpn(): InstantPaymentNotification
    {
        $expectedIpn = new InstantPaymentNotification();
        $expectedIpn->cardMask = 'xxxx-xxxx-xxxx-0425';
        $expectedIpn->salt = 'test-salt';
        $expectedIpn->orderRef = 'test-order-ref';
        $expectedIpn->method = 'CARD';
        $expectedIpn->merchant = 'test-merchant';
        $expectedIpn->finishDate = '2019-09-01T00:12:42+02:00';
        $expectedIpn->expiry = '2021-04-02T00:00:00+02:00';
        $expectedIpn->paymentDate = '2019-09-02T00:12:42+02:00';
        $expectedIpn->transactionId = 42;
        $expectedIpn->status = 'FINISHED';
        $expectedIpn->receiveDate = '2019-09-03T00:12:42+02:00';

        return $expectedIpn;
    }
}
