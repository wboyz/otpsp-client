<?php

namespace Cheppers\OtpspClient\DataType;

class Product extends RedirectBase
{
    /**
     * @var string
     */
    public $productName = '';

    /**
     * @var string
     */
    public $sku = '';

    /**
     * @var string
     */
    public $description = '';

    /**
     * @var float
     */
    public $price = 0.0;

    /**
     * @var int
     */
    public $quantity = 0;

    /**
     * @var int
     */
    public $discount = 0;

    /**
     * @var int
     */
    public $vat = 0;

    protected static $propertyMapping = [
        'productName' => 'ORDER_PNAME[]',
        'sku' => 'ORDER_PCODE[]',
        'description' => 'ORDER_PINFO[]',
        'price' => 'ORDER_PRICE[]',
        'quantity' => 'ORDER_QTY[]',
        'discount' => 'DISCOUNT[]',
        'vat' => 'ORDER_VAT[]',
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
        return $this->sku === '';
    }
}
