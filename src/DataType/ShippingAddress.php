<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class ShippingAddress extends RedirectBase
{
    /**
     * @var string
     */
    public $firstName = '';

    /**
     * @var string
     */
    public $lastName = '';

    /**
     * @var string
     */
    public $countryCode = '';

    /**
     * @var string
     */
    public $city = '';

    /**
     * @var string
     */
    public $addressLine = '';

    /**
     * @var string
     */
    public $addressLine2 = '';

    /**
     * @var string
     */
    public $postalCode = '';

    protected static $propertyMapping = [
        'firstName' => 'DELIVERY_FNAME',
        'lastName' => 'DELIVERY_LNAME',
        'countryCode' => 'DELIVERY_COUNTRYCODE',
        'city' => 'DELIVERY_CITY',
        'addressLine' => 'DELIVERY_ADDRESS',
        'addressLine2' => 'DELIVERY_ADDRESS2',
        'postalCode' => 'DELIVERY_ZIPCODE',
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
        return $this->postalCode === '';
    }
}
