<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

abstract class RedirectBase
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

    public function exportData(): string
    {
        if ($this->isEmpty()) {
            return '';
        }

        $data = [];
        foreach (array_keys(get_object_vars($this)) as $key) {
            $value =  $this->{$key};
            if (!in_array($key, $this->requiredFields) && !$value) {
                continue;
            }

            $data[] = [
                $key => $value,
            ];
        }

        return json_encode($data);
    }
}
