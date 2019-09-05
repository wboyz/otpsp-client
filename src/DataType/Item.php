<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class Item extends Base
{
    /**
     * @var string
     */
    public $ref = '';

    /**
     * @var string
     */
    public $title = '';

    /**
     * @var string
     */
    public $description = '';

    /**
     * @var int
     */
    public $amount = 0;

    /**
     * @var float
     */
    public $price = 0.0;

    /**
     * @var int
     */
    public $tax = '0';
}
