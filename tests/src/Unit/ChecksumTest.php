<?php

declare(strict_types=1);

namespace Cheppers\OtpspClient\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Cheppers\OtpspClient\Checksum;

/**
 * Class SerializerTest
 * @covers \Cheppers\OtpspClient\Checksum
 * @package Cheppers\OtpspClient\Tests\Unit
 */
class ChecksumTest extends TestCase
{
    public function casesEncodeSuccess(): array
    {
        return [
            'Data empty array, return empty string' => [[], '', ''],
            'Real test with 1 product' => [
                [
                    'PUBLICTESTHUF',
                    '101010514601159878253',
                    '2016-04-08 11:46:27',
                    'Lorem ipsum',
                    'sku0001',
                    'ÁRVÍZTŰRŐ TÜKÖRFÚRÓGÉP',
                    '331',
                    '1',
                    '0',
                    '0',
                    'HUF',
                    '0',
                    'CCVISAMC',
                ],
                'FxDa5w314kLlNseq2sKuVwaqZshZT5d6',
                '51f48bfda333a8c477bbbedd18a1f787'
            ],
            'Real test with 2 products' => [
                [
                    'PUBLICTESTHUF',
                    '101010514601278769072',
                    '2016-04-08 15:04:36',
                    'Lorem ipsum',
                    'Dolor sit amet',
                    'sku0001',
                    'sku0002',
                    'ÁRVÍZTŰRŐ',
                    'TÜKÖRFÚRÓGÉP',
                    '123',
                    '456',
                    '1',
                    '1',
                    '0',
                    '0',
                    '0',
                    'HUF',
                    '0',
                    'CCVISAMC',
                ],
                'FxDa5w314kLlNseq2sKuVwaqZshZT5d6',
                '6ed529adde57070bf64ce05efa559307'
            ],
        ];
    }

    /**
     * @dataProvider casesEncodeSuccess
     */
    public function testEncodeSuccess(array $data, string $secretKey, string $expected): void
    {
        $serializer = new Checksum();
        $result = $serializer->encode($data, $secretKey);

        static::assertSame($expected, $result);
    }

    public function casesEncodeFail(): array
    {
        return [
            'Multi dimension array, should return exception' => [
                [
                    'test' => [
                        'test data',
                        'test data2',
                    ],
                    'test2' => [
                        'test data3',
                        'test data4',
                    ]
                ],
                '',
            ],
        ];
    }

    /**
     * @dataProvider casesEncodeFail
     */
    public function testEncodeFail(array $data, string $secretKey): void
    {
        $serializer = new Checksum();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Data can not be multidimensional array.');

        $serializer->encode($data, $secretKey);
    }
}
