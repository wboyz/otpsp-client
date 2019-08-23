<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class RefundResponse extends ResponseBase
{
    /**
     * @var int
     */
    public $refundTransactionId = 0;

    /**
     * @var float
     */
    public $refundTotal = 0.0;

    /**
     * @var float
     */
    public $remainingTotal = 0.0;
}
