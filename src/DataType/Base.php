<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class Base
{

    /**
     * @var string[]
     */
    protected static $propertyMapping = [
        'ORDER_DATE' => 'orderDate',
        'REFNO' => 'refNo',
        'REFNOEXT' => 'refNoExt',
        'ORDER_STATUS' => 'orderStatus',
        'PAYMETHOD' => 'payMethod',
        'ORDER_REF' => 'orderRef',
        'STATUS_CODE' => 'statusCode',
        'STATUS_NAME' => 'statusName',
        'IRN_DATE' => 'irnDate',
        'IDN_DATE' => 'idnDate',
        'RC' => 'returnCode',
        'RT' => 'returnText',
        '3dsecure' => 'secure',
        'date' => 'date',
        'payrefno' => 'payRefNo',
        'ctrl' => 'ctrl',
        'ORDERSTATUS' => 'orderStatus',
        'IPN_PID' => 'ipnPId',
        'IPN_PNAME' => 'ipnPName',
        'IPN_DATE' => 'ipnDate',
        'HASH' => 'hash',
    ];

    public static function __set_state($values)
    {
        $instance = new static();
        foreach (static::$propertyMapping as $external => $internal) {
            if (!array_key_exists($external, $values) || !property_exists($instance, $internal)) {
                continue;
            }

            if ($external === 'STATUS_CODE') {
                settype($values[$external], 'int');
            }

            $instance->{$internal} = $values[$external];
        }

        return $instance;
    }

    /**
     * External property names to exclude from export.
     *
     * @var string[]
     */
    protected $excludeFromExport = [
        'HASH',
    ];

    public function exportForChecksum(): array
    {
        $values = [];

        foreach (static::$propertyMapping as $external => $internal) {
            if (!property_exists($this, $internal) || in_array($external, $this->excludeFromExport)) {
                continue;
            }

            $values[$external] = $this->{$internal};
        }

        return $values;
    }
}
