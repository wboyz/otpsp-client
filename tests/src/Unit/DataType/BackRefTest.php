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
    public function casesExportForChecksum(): array
    {

        return [
            'valid' => [
                [
                    '123-456',
                    '001',
                    '001 | foo',
                    'no',
                    '2019-01-01 12:15:10',
                    '1000',
                ],
                [
                    'REFNOEXT' => '123-456',
                    'RC' => '001',
                    'RT' => '001 | foo',
                    '3dsecure' => 'no',
                    'date' => '2019-01-01 12:15:10',
                    'payrefno' => '1000',
                    'ctrl' => '500',
                ],
            ],
        ];
    }
}
