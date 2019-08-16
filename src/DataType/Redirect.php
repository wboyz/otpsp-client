<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class Redirect extends RedirectBase
{

    public static function __set_state($values)
    {
        /** @var static $instance */
        $instance = parent::__set_state($values);

        $keys = [
            'delivery' => Delivery::class,
            'invoice' => Invoice::class,
        ];
        foreach ($keys as $key => $className) {
            if (array_key_exists($key, $values) && is_array($values[$key])) {
                $instance->{$key} = $className::__set_state($values[$key]);
            }
        }

        if (array_key_exists('items', $values)) {
            foreach ($values['products'] as $key => $productValues) {
                $instance->items[] = $productValues instanceof Item ?
                    $productValues
                    : Item::__set_state($productValues);
            }
        }

        return $instance;
    }

    /**
     * @var string
     */
    public $merchant = '';

    /**
     * @var string
     */
    public $orderRef = '';

    /**
     * @var string
     */
    public $customer = '';

    /**
     * @var string
     */
    public $customerEmail = '';

    /**
     * @var string
     */
    public $language = '';

    /**
     * @var string
     */
    public $currency = '';

    /**
     * @var string
     */
    public $total = '';

    /**
     * @var string
     *
     * @todo We need 32character random string.
     */
    public $salt = '98717fead01f2881cf39efba7390068e';

    /**
     * @var string[]
     */
    public $methods = ['CARD'];

    /**
     * @var Invoice
     */
    public $invoice;

    /**
     * @var Delivery
     */
    public $delivery;

    /**
     * @var Item[]
     */
    public $items;

    /**
     * @var string
     */
    public $timeout = '';

    /**
     * @var string
     */
    public $url = '';

    /**
     * @var string
     */
    public $sdkVersion = 'SimplePay_PHP_SDK_2.0_180930:33ccd5ed8e8a965d18abfae333404184';

    /**
     * {@inheritdoc}
     */
    protected $requiredFields = [
        'merchant',
        'orderRef',
        'customer',
        'customerEmail',
        'language',
        'currency',
        'total',
        'salt',
        'methods',
        'invoice',
        'timeout',
        'url',
        'sdkVersion',
    ];

    /**
     * {@inheritdoc}
     */
    public function exportData(): string
    {
        if ($this->isEmpty()) {
            return '';
        }

        $data = [];

        foreach (array_keys(get_object_vars($this)) as $key) {
            switch ($key) {
                case 'merchant':
                    $data['merchant'] = $this->merchant;
                    break;
                case 'orderRef':
                    $data['orderRef'] = $this->orderRef;
                    break;
                case 'customer':
                    $data['customer'] = $this->customer;
                    break;
                case 'customerEmail':
                    $data['customerEmail'] = $this->customerEmail;
                    break;
                case 'language':
                    $data['language'] = $this->language;
                    break;
                case 'currency':
                    $data['currency'] = $this->currency;
                    break;
                case 'total':
                    $data['total'] = $this->total;
                    break;
                case 'salt':
                    $data['salt'] = $this->salt;
                    break;
                case 'methods':
                    $data['methods'] = $this->methods;
                    break;
                case 'invoice':
                    $data['invoice'] = $this->invoice->exportData();
                    break;
                case 'delivery':
                    $data['delivery'] = $this->delivery->exportData();
                    break;
                case 'items':
                    foreach ($this->items as $item) {
                        $data['items'][] = $item->exportData();
                    }
                    break;
                case 'timeout':
                    $data['timeout'] = $this->timeout;
                    break;
                case 'url':
                    $data['url'] = $this->url;
                    break;
                case 'sdkVersion':
                    $data['sdkVersion'] = $this->sdkVersion;
                    break;
            }
        }

        return json_encode($data);
    }
}
