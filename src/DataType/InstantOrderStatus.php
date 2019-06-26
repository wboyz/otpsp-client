<?php

declare(strict_types = 1);

namespace Cheppers\OtpClient\DataType;

class InstantOrderStatus extends Base
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
