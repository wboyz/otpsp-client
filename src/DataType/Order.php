<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class Order extends RedirectBase
{

    /**
     * {@inheritdoc}
     */
    protected static $propertyMapping = [
        'paymentId' => 'ORDER_REF',
        'orderDate' => 'ORDER_DATE',
        'currency' => 'PRICES_CURRENCY',
        'shippingPrice' => 'ORDER_SHIPPING',
    ];

    /**
     * {@inheritdoc}
     */
    protected $requiredFields = [
        'paymentId',
        'orderDate',
        'currency',
    ];

    /**
     * @var string
     */
    public $paymentId = '';

    /**
     * @var string
     */
    public $orderDate = '';

    /**
     * @var string
     */
    public $currency = '';

    /**
     * @var float
     */
    public $shippingPrice = 0.0;

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->paymentId === '';
    }
}
