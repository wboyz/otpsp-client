<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class InstantRefundNotification extends ResponseBase
{

    /**
     * @var string
     */
    public $orderRef = '';

    /**
     * @var string
     */
    public $statusCode = '';

    /**
     * @var string
     */
    public $statusName = '';

    /**
     * @var string
     */
    public $irnDate = '';
}
