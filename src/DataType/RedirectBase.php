<?php

namespace Cheppers\OtpspClient\DataType;

abstract class RedirectBase
{
    protected static $propertyMapping = [];

    abstract protected function isEmpty():bool;

    public function exportData(): array
    {
        if ($this->isEmpty()) {
            return [];
        }

        $data = [];
        foreach (static::$propertyMapping as $internal => $external) {
            $value =  $this->{$internal};
            if (!$value) {
                continue;
            }

            $data[] = [
                $external => $value,
            ];
        }

        return $data;
    }
}
