<?php

namespace Cheppers\OtpspClient\DataType;

class Order extends RedirectBase
{
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

    protected static $propertyMapping = [
        'paymentId' => 'ORDER_REF',
        'orderDate' => 'ORDER_DATE',
        'currency' => 'PRICES_CURRENCY',
        'shippingPrice' => 'ORDER_SHIPPING',
    ];

    public static function __set_state($values)
    {
        $instance = new static();
        foreach (static::$propertyMapping as $internal => $external) {
            if (!array_key_exists($internal, $values) || !property_exists($instance, $external)) {
                continue;
            }

            $instance->{$external} = $values[$internal];
        }

        return $instance;
    }

    protected function isEmpty(): bool
    {
        return $this->paymentId === '';
    }
}
