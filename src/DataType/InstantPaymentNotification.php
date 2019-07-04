<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class InstantPaymentNotification extends Base
{

    /**
     * @var string
     */
    public $refNoExt = '';

    /**
     * @var string
     */
    public $refNo = '';

    /**
     * @var string
     */
    public $orderStatus = '';

    /**
     * @var array
     */
    public $ipnPId = [];

    /**
     * @var array
     */
    public $ipnPName = [];

    /**
     * @var string
     */
    public $ipnDate = '';

    /**
     * @var string
     */
    public $hash = '';
}
