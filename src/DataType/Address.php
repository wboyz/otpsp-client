<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class Address extends Base
{
    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $company = '';

    /**
     * @var string
     */
    public $country = '';

    /**
     * @var string
     */
    public $state = '';

    /**
     * @var string
     */
    public $city = '';

    /**
     * @var string
     */
    public $zip = '';

    /**
     * @var string
     */
    public $address = '';

    /**
     * @var string
     */
    public $address2 = '';

    /**
     * @var string
     */
    public $phone = '';

    /**
     * {@inheritdoc}
     */
    protected $requiredFields = [
        'name',
        'city',
        'country',
        'address',
        'address2',
        'zip',
        'state',
    ];
}
