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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function casesExportForChecksum(): array {
        return [];
    }
}
