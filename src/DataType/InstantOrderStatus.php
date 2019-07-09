<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class InstantOrderStatus extends ResponseBase
{

    /**
     * @var string
     */
    public $orderDate = '';

    /**
     * @var string
     */
    public $refNo = '';

    /**
     * @var string
     */
    public $refNoExt = '';

    /**
     * @var string
     */
    public $orderStatus = '';

    /**
     * @var string
     */
    public $payMethod = '';
}
