<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\BackRef;

/**
 * @covers \Cheppers\OtpspClient\DataType\BackRef<extended>
 */
class BackRefTest extends TestBase
{
    /**
     * {@inheritdoc}
     */
    protected $className = BackRef::class;

    public function casesSetState(): array
    {
        return [
            'basic' => [
                [
                    'refNoExt' => 'a',
                    'returnCode' => 'b',
                    'returnText' => 'c',
                    'secure' => 'd',
                    'date' => 'e',
                    'payRefNo' => 'f',
                    'ctrl' => 'g',
                ],
                [
                    'REFNOEXT' => 'a',
                    'RC' => 'b',
                    'RT' => 'c',
                    '3dsecure' => 'd',
                    'date' => 'e',
                    'payrefno' => 'f',
                    'ctrl' => 'g',
                ],
            ],
        ];
    }
}
