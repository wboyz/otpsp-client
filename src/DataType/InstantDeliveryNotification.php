<?php

declare(strict_types = 1);

namespace Cheppers\OtpClient\DataType;

class InstantDeliveryNotification extends Base
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
    public $idnDate = '';
}
