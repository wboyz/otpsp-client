<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class Product extends RedirectBase
{

    /**
     * {@inheritdoc}
     */
    protected static $propertyMapping = [
        'productName' => 'ORDER_PNAME[]',
        'sku' => 'ORDER_PCODE[]',
        'description' => 'ORDER_PINFO[]',
        'price' => 'ORDER_PRICE[]',
        'quantity' => 'ORDER_QTY[]',
        'vat' => 'ORDER_VAT[]',
    ];

    /**
     * {@inheritdoc}
     */
    protected $requiredFields = [
        'productName',
        'sku',
        'price',
        'quantity',
        'vat',
    ];

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
    public $vat = '0';

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->sku === '';
    }
}
