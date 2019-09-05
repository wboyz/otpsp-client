<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class Base
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

    public function exportData(): array
    {

        $data = [];
        foreach (array_keys(get_object_vars($this)) as $key) {
            $value =  $this->{$key};
            if (!isset($value)) {
                continue;
            }

            $data[$key] = $value;
        }

        return $data;
    }
}
