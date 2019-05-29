<?php

declare(strict_types=1);

namespace Cheppers\OtpClient\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Cheppers\OtpClient\Serializer;

/**
 * Class SerializerTest
 * @covers \Cheppers\OtpClient\Serializer
 * @package Cheppers\OtpClient\Tests\Unit
 */
class SerializerTest extends TestCase
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
        $serializer = new Serializer();
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
        $serializer = new Serializer();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Data can not be multidimensional array.');

        $serializer->encode($data, $secretKey);
    }

    public function casesDecode(): array
    {
        return [
            'Empty url given, should return exception' => [
                '',
                '',
                '',
            ],
            'Real test' => [
                'https://weboldalam.tld/backref.php?order_ref=101010514611570269664'
                . '&order_currency=HUF&RC=000&RT=000+%7C+OK&3dsecure=NO'
                . '&date=2016-04-20+14%3A57%3A38&payrefno=99016530&ctrl=a5a268fd200eaef93e87a3f1403ce65f',
                'FxDa5w314kLlNseq2sKuVwaqZshZT5d6',
                'a5a268fd200eaef93e87a3f1403ce65f',
            ],
        ];
    }

    /**
     * @dataProvider casesDecode
     */
    public function testDecode(string $url, string $secretKey, string $expected): void
    {
        $serializer = new Serializer();
        $result = $serializer->decode($url, $secretKey);

        static::assertSame($expected, $result);
    }

    public function casesIsUrlValid(): array
    {
        return [
            'Good url test' => [
                'https://weboldalam.tld/backref.php?'
                . 'order_ref=101010514611570269664&order_currency=HUF&RC=000&RT=000+%7C+OK&3dsecure=NO'
                . '&date=2016-04-20+14%3A57%3A38&payrefno=99016530&ctrl=a5a268fd200eaef93e87a3f1403ce65f',
                'FxDa5w314kLlNseq2sKuVwaqZshZT5d6',
                true,
            ],
            'Bad url test' => [
                'https://weboldalam.tld/backref.php?order_ref=101010514611570269664'
                . '&order_currency=HUF&RC=000&RT=000+%7C+OK&3dsecure=NO'
                . '&date=2016-04-20+14%3A57%3A38&payrefno=99016530&ctrl=a1lx1r8mlviq2uzbjdgifnt90z19ldbm',
                'FxDa5w314kLlNseq2sKuVwaqZshZT5d6',
                false,
            ],
        ];
    }

    /**
     * @dataProvider casesIsUrlValid
     */
    public function testIsUrlValid(string $url, string $secretKey, bool $expected): void
    {
        $serializer = new Serializer();
        $result = $serializer->isUrlValid($url, $secretKey);

        static::assertSame($expected, $result);
    }
}
