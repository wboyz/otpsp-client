<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class StartResponse extends ResponseBase
{
    /**
     * @var string
     */
    public $salt = '';

    /**
     * @var string
     */
    public $merchant = '';

    /**
     * @var string
     */
    public $orderRef = '';

    /**
     * @var string
     */
    public $currency = '';

    /**
     * @var int
     */
    public $transactionId = 0;

    /**
     * @var string
     */
    public $timeout = '';

    /**
     * @var double
     */
    public $total = 0;

    /**
     * @var string
     */
    public $paymentUrl = '';
}
