<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class BackResponse
{
    /**
     * @var string[]
     */
    protected static $propertyMapping = [
        'r' => 'responseCode',
        't' => 'transactionId',
        'e' => 'event',
        'm' => 'merchant',
        'o' => 'orderId',
    ];

    public static function __set_state($values)
    {
        $instance = new static();
        foreach (static::$propertyMapping as $external => $internal) {
            if (!array_key_exists($external, $values) || !property_exists($instance, $internal)) {
                continue;
            }

            $instance->{$internal} = $values[$external];
        }

        return $instance;
    }

    /**
     * @var int
     */
    public $responseCode = 0;

    /**
     * @var int
     */
    public $transactionId = 0;

    /**
     * @var string
     */
    public $event = '';

    /**
     * @var string
     */
    public $merchant = '';

    /**
     * @var string
     */
    public $orderId = '';
}
