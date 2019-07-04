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

    public $refNoExt = '';

    public $refNo = '';

    public $orderStatus= '';

    public $ipnPId = '';

    public $ipnPName = '';

    public $ipnDate = '';

    public $hash = '';

    public static function __set_state($values)
    {
        $self = new static();
        foreach (static::$propertyMapping as $src => $dst) {
            if (!array_key_exists($src, $values) || !property_exists($self, $dst)) {
                continue;
            }

            if ($src === 'STATUS_CODE') {
                settype($values[$src], 'int');
            }

            $self->{$dst} = $values[$src];
        }

        return $self;
    }
}
