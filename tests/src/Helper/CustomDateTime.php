<?php

declare(strict_types=1);

namespace Cheppers\OtpspClient\Tests\Helper;

use DateTime;

class CustomDateTime extends DateTime
{
    public function format($format)
    {
        return '2019-09-03T00:12:42+02:00';
    }
}
