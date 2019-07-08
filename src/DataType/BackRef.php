<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\DataType;

use Cheppers\OtpspClient\OtpSimplePayClientInterface;

class BackRef extends Base
{

    /**
     * {@inheritdoc}
     */
    protected $excludeFromExport = [
        OtpSimplePayClientInterface::CONTROL_KEY,
    ];

    /**
     * @var string
     */
    public $refNoExt = '';

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
    public $secure = '';

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
