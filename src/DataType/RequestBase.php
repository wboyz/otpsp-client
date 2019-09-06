<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

use JsonSerializable;

abstract class RequestBase implements JsonSerializable
{

    public static function __set_state($values)
    {
        $instance = new static();
        foreach (array_keys(get_object_vars($instance)) as $key) {
            if (!array_key_exists($key, $values)) {
                continue;
            }

            $instance->{$key} = $values[$key];
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
    public $salt = '';

    /**
     * @var string
     */
    public $currency = '';

    /**
     * @var string
     */
    public $sdkVersion = 'SimplePay_PHP_SDK_2.0_180930:33ccd5ed8e8a965d18abfae333404184';
}
