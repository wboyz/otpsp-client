<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Cheppers\OtpspClient\Checksum;

/**
 * @covers \Cheppers\OtpspClient\Checksum
 */
class ChecksumTest extends TestCase
{
    /**
     * @var string
     */
    protected $secretKey = 'FxDa5w314kLlNseq2sKuVwaqZshZT5d6';

    public function casesCalculateSuccess(): array
    {
        return [
            'Data empty array, return empty string' => [
                '',
                [],
            ],
            'Real test with 1 product' => [
                '51f48bfda333a8c477bbbedd18a1f787',
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
            ],
            'Real test with 2 products' => [
                '6ed529adde57070bf64ce05efa559307',
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
            ],
        ];
    }

    /**
     * @dataProvider casesCalculateSuccess
     */
    public function testCalculateSuccess(string $expected, array $data): void
    {
        $actual = (new Checksum())->calculate($data, $this->secretKey);

        static::assertSame($expected, $actual);
    }

    public function casesCalculateFail(): array
    {
        return [
            'Multi dimension array, should return exception' => [
                [
                    'class' => InvalidArgumentException::class,
                    'message' => 'Data can not be multidimensional array.',
                    'code' => 1,
                ],
                [
                    'test' => [
                        'test data',
                        'test data2',
                    ],
                    'test2' => [
                        'test data3',
                        'test data4',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesCalculateFail
     */
    public function testCalculateFail(array $expected, array $data): void
    {
        if (array_key_exists('class', $expected)) {
            $this->expectException($expected['class']);
        }

        if (array_key_exists('message', $expected)) {
            $this->expectExceptionMessage($expected['message']);
        }

        if (array_key_exists('code', $expected)) {
            $this->expectExceptionCode($expected['code']);
        }

        (new Checksum())->calculate($data, $this->secretKey);
    }
}
