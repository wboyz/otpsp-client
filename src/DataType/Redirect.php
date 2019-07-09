<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class Redirect extends RedirectBase
{

    /**
     * {@inheritdoc}
     */
    protected static $propertyMapping = [
        'merchantId' => 'MERCHANT',
        'customerEmail' => 'BILL_EMAIL',
        'timeoutUrl' => 'TIMEOUT_URL',
        'backrefUrl' => 'BACK_REF',
        'langCode' => 'LANGUAGE',
    ];

    public static function __set_state($values)
    {
        /** @var static $instance */
        $instance = parent::__set_state($values);

        $keys = [
            'order' => Order::class,
            'shippingAddress' => ShippingAddress::class,
            'billingAddress' => BillingAddress::class,
        ];
        foreach ($keys as $key => $className) {
            if (array_key_exists($key, $values) && is_array($values[$key])) {
                $values[$key] = $className::__set_state($values[$key]);
            }
        }

        if (array_key_exists('products', $values)) {
            foreach ($values['products'] as $key => $productValues) {
                $instance->products[] = $productValues instanceof Product ?
                    $productValues
                    : Product::__set_state($productValues);
            }
        }

        return $instance;
    }

    /**
     * @var string
     */
    public $merchantId = '';

    /**
     * @var string
     */
    public $customerEmail = '';

    /**
     * @var string
     */
    public $timeoutUrl = '';

    /**
     * @var string
     */
    public $backrefUrl = '';

    /**
     * @var string
     */
    public $langCode = '';

    /**
     * @var Order
     */
    public $order;

    /**
     * @var Product[]
     */
    public $products = [];

    /**
     * @var ShippingAddress
     */
    public $shippingAddress;

    /**
     * @var BillingAddress
     */
    public $billingAddress;

    /**
     * @var string[]
     */
    protected $hashFields = [
        'MERCHANT',
        'ORDER_REF',
        'ORDER_DATE',
        'ORDER_PNAME[]',
        'ORDER_PCODE[]',
        'ORDER_PINFO[]',
        'ORDER_PRICE[]',
        'ORDER_QTY[]',
        'ORDER_VAT[]',
        'ORDER_SHIPPING',
        'PRICES_CURRENCY',
        'DISCOUNT',
    ];

    public function __construct()
    {
        $this->order = new Order();
        $this->shippingAddress = new ShippingAddress();
        $this->billingAddress = new BillingAddress();
    }

    /**
     * {@inheritdoc}
     */
    protected function isEmpty(): bool
    {
        return $this->merchantId === '';
    }

    /**
     * {@inheritdoc}
     */
    public function exportData(): array
    {
        $data = parent::exportData();
        $data = array_merge($data, $this->order->exportData());
        $data = array_merge($data, $this->shippingAddress->exportData());
        $data = array_merge($data, $this->billingAddress->exportData());
        foreach ($this->products as $product) {
            $data = array_merge($data, $product->exportData());
        }

        return $data;
    }

    protected function getHashValues(): array
    {
        $values = [];
        foreach ($this->exportData() as $items) {
            foreach ($items as $key => $value) {
                if (!in_array($key, $this->hashFields)) {
                    continue;
                }

                $values[] = $value;
            }
        }

        return $values;
    }
}
