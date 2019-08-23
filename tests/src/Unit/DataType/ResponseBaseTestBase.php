<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\ResponseBase;
use PHPUnit\Framework\TestCase;

abstract class ResponseBaseTestBase extends TestCase
{

    /**
     * @var string|\Cheppers\OtpspClient\DataType\ResponseBase
     */
    protected $className = '';

    abstract public function casesSetState(): array;

    /**
     * @dataProvider casesSetState
     */
    public function testSetState(ResponseBase $expected, array $values): void
    {
        static::assertEquals($expected, $this->className::__set_state($values));
    }
}
