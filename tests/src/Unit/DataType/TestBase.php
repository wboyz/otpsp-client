<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use PHPUnit\Framework\TestCase;

abstract class TestBase extends TestCase
{

    /**
     * @var string|\Cheppers\OtpspClient\DataType\Base
     */
    protected $className = '';

    abstract public function casesSetState(): array;

    /**
     * @dataProvider casesSetState
     */
    public function testSetState(array $expected, array $values): void
    {
        $data = $this->className::__set_state($values);
        foreach ($expected as $key => $value) {
            static::assertSame($value, $data->{$key});
        }
    }

    abstract public function casesExportForChecksum(): array;

    /**
     * @dataProvider casesExportForChecksum
     */
    public function testExportForChecksum(array $expected, array $values): void
    {
        $data = $this->className::__set_state($values);
        static::assertSame($expected, $data->exportForChecksum());
    }
}
