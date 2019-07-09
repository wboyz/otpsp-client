<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class BillingAddress extends ShippingAddress
{

    /**
     * {@inheritdoc}
     */
    protected static $propertyMapping = [
        'firstName' => 'BILL_FNAME',
        'lastName' => 'BILL_LNAME',
        'organization' => 'BILL_COMPANY',
        'countryCode' => 'BILL_COUNTRYCODE',
        'city' => 'BILL_CITY',
        'addressLine' => 'BILL_ADDRESS',
        'addressLine2' => 'BILL_ADDRESS2',
        'postalCode' => 'BILL_ZIPCODE',
    ];

    /**
     * @var string
     */
    public $organization = '';
}
