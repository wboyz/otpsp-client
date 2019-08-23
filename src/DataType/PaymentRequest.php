<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class PaymentRequest extends RequestBase
{

    public static function __set_state($values)
    {
        $instance = new static();

        foreach (array_keys(get_object_vars($instance)) as $key) {
            if (!array_key_exists($key, $values)) {
                continue;
            }

            switch ($key) {
                case 'delivery':
                    $instance->delivery = Address::__set_state($values['delivery']);
                    break;
                case 'invoice':
                    $instance->invoice = Address::__set_state($values['invoice']);
                    break;
                case 'urls':
                    $instance->urls = Urls::__set_state($values['urls']);
                    break;
                case 'items':
                    foreach ($values['items'] as $item) {
                        $instance->items[] = Item::__set_state($item);
                    }
                    break;
                default:
                    $instance->{$key} = $values[$key];
            }
        }

        return $instance;
    }

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
    public $total = '';

    /**
     * @var string[]
     */
    public $methods = ['CARD'];

    /**
     * @var Address
     */
    public $invoice;

    /**
     * @var Address
     */
    public $delivery;

    /**
     * @var Item[]
     */
    public $items = [];

    /**
     * @var int
     */
    public $shippingCost = 0;

    /**
     * @var int
     */
    public $discount = 0;

    /**
     * @var string
     */
    public $timeout = '';

    /**
     * @var string
     */
    public $url = '';

    /**
     * @var Urls
     */
    public $urls;


    public function __construct()
    {
        $this->invoice = new Address();
        $this->delivery = new Address();
        $this->urls = new Urls();
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
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
                case 'shippingCost':
                    $data['shippingCost'] = $this->shippingCost;
                    break;
                case 'discount':
                    $data['discount'] = $this->discount;
                    break;
                case 'timeout':
                    $data['timeout'] = $this->timeout;
                    break;
                case 'url':
                    $data['url'] = $this->url;
                    break;
                case 'urls':
                    if ($this->urls->success === ''
                        && $this->urls->cancel === ''
                        && $this->urls->fail === ''
                        && $this->urls->timeout === ''
                    ) {
                        break;
                    }
                    $data['urls'] = $this->urls->exportData();
                    break;
                case 'sdkVersion':
                    $data['sdkVersion'] = $this->sdkVersion;
                    break;
            }
        }

        return $data;
    }
}
