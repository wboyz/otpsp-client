<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class RefundRequest extends RequestBase
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

    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }
}
