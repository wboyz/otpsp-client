<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit;

use Cheppers\OtpspClient\Utils;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Cheppers\OtpspClient\Utils<extended>
 */
class UtilsTest extends TestCase
{
    public function casesFlatArray()
    {
        return [
            'empty array' => [
                [],
                [],
                []
            ],
            'one dimensional array, no skip' => [
                [
                    'bar',
                    'bak',
                ],
                [
                    'foo' => 'bar',
                    'baz' => 'bak',
                ],
                [],
            ],
            'one dimensional array with skip' => [
                [
                    'bar',
                ],
                [
                    'foo' => 'bar',
                    'baz' => 'bak',
                ],
                [
                    'baz'
                ],
            ],
            'multi dimensional array, no skip' => [
                [
                    'one',
                    'two',
                    'three',
                    'bak',
                ],
                [
                    'foo' => [
                        'one',
                        'two',
                        'three',
                    ],
                    'baz' => 'bak',
                ],
                [],
            ],
            'multi dimensional array with skip' => [
                [
                    'one',
                    'two',
                    'three',
                ],
                [
                    'foo' => [
                        'one',
                        'two',
                        'three',
                    ],
                    'baz' => 'bak',
                ],
                [
                    'baz'
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesFlatArray
     */
    public function testFlatArray(array $expected, array $given, array $skip)
    {
        $actual = (new Utils())->flatArray($given, $skip);

        static::assertSame($expected, $actual);
    }
}
