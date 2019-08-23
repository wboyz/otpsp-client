<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class PaymentResponse extends ResponseBase
{

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

    /**
     * @var array
     */
    public $errorCodes = [];
}
