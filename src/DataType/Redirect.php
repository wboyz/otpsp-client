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

    /**
     * {@inheritdoc}
     */
    protected $requiredFields = [
        'merchantId',
        'timeoutUrl',
        'backrefUrl',
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
                $instance->{$key} = $className::__set_state($values[$key]);
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
        'DISCOUNT[]',
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
    public function isEmpty(): bool
    {
        return !$this->products || $this->order->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function exportData(): array
    {
        if ($this->isEmpty()) {
            return [];
        }

        $data = [
            [
                'MERCHANT' => $this->merchantId,
            ],
            [
                'ORDER_REF' => $this->order->paymentId,
            ],
            [
                'ORDER_DATE' => $this->order->orderDate,
            ],
        ];

        foreach ($this->products as $product) {
            $data = array_merge($data, $product->exportData());
        }

        $data[] = [
            'ORDER_SHIPPING' => $this->order->shippingPrice,
        ];

        $data[] = [
            'PRICES_CURRENCY' => $this->order->currency,
        ];

        $data[] = [
            'TIMEOUT_URL' => $this->timeoutUrl,
        ];

        $data[] = [
            'BACK_REF' => $this->backrefUrl,
        ];

        $data[] = [
            'BILL_EMAIL' => $this->customerEmail,
        ];

        $data = array_merge(
            $data,
            $this->billingAddress->exportData(),
            $this->shippingAddress->exportData()
        );

        if ($this->langCode) {
            $data[] = [
                'LANGUAGE' => $this->langCode,
            ];
        }

        return $data;
    }

    public function getHashValues(array $data): array
    {
        $groups = [
            'pre' => [],
            'ORDER_PNAME[]' => [],
            'ORDER_PCODE[]' => [],
            'ORDER_PINFO[]' => [],
            'ORDER_PRICE[]' => [],
            'ORDER_QTY[]' => [],
            'DISCOUNT[]' => [],
            'ORDER_VAT[]' => [],
            'post' => [],
        ];
        foreach ($data as $items) {
            foreach ($items as $key => $value) {
                if (!in_array($key, $this->hashFields)) {
                    continue;
                }

                switch ($key) {
                    case 'MERCHANT':
                    case 'ORDER_REF':
                    case 'ORDER_DATE':
                        $group = 'pre';
                        break;

                    case 'ORDER_PNAME[]':
                    case 'ORDER_PCODE[]':
                    case 'ORDER_PINFO[]':
                    case 'ORDER_PRICE[]':
                    case 'ORDER_QTY[]':
                    case 'DISCOUNT[]':
                    case 'ORDER_VAT[]':
                        $group = $key;
                        break;

                    default:
                        $group = 'post';
                }

                $groups[$group][] = $value;
            }
        }

        $values = [];
        foreach ($groups as $group => $items) {
            foreach ($items as $item) {
                $values[] = $item;
            }
        }

        return $values;
    }
}
