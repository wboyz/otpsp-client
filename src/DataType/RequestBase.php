<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

abstract class RequestBase implements \JsonSerializable
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
}
