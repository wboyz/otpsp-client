<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class ResponseBase
{

    public static function __set_state($values)
    {
        $instance = new static();
        $properties = array_keys(get_object_vars($instance));
        foreach ($values as $key => $value) {
            if (!in_array($key, $properties)) {
                continue;
            }

            $instance->{$key} = $value;
        }

        return $instance;
    }
}
