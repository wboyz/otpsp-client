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

    /**
     * Internal name of the required fields.
     *
     * @var string[]
     */
    protected $requiredFields = [];

    public function isEmpty(): bool
    {
        foreach ($this->requiredFields as $requiredField) {
            if (!isset($this->{$requiredField})) {
                return true;
            }
        }

        return false;
    }
}
