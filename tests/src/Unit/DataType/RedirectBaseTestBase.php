<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use PHPUnit\Framework\TestCase;

abstract class RedirectBaseTestBase extends TestCase
{
    /**
     * @var string|\Cheppers\OtpspClient\DataType\RedirectBase
     */
    protected $className = '';

    abstract public function casesExportData();

    /**
     * @dataProvider casesExportData
     */
    public function testExportData(array $expected, array $values)
    {
        $data = $this->className::__set_state($values);
        static::assertSame($expected, $data->exportData());
    }
}
