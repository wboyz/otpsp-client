<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

class Backref extends Base
{

    /**
     * @var string
     */
    public $returnCode = '';

    /**
     * @var string
     */
    public $returnText = '';

    /**
     * @var string
     */
    public $secure= '';

    /**
     * @var string
     */
    public $date = '';

    /**
     * @var string
     */
    public $payRefNo = '';

    /**
     * @var string
     */
    public $ctrl = '';
}
