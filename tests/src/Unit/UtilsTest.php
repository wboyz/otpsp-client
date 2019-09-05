<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit;

use Cheppers\OtpspClient\Utils;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Cheppers\OtpspClient\Utils
 */
class UtilsTest extends TestCase
{
    public function casesGetQueryFromUrl(): array
    {
        return [
            'invalid url' => [[], 'test'],
            'basic' => [
                [
                    'a' => '42',
                    'b' => 'query-variable'
                ],
                'http://example.com?a=42&b=query-variable',
            ],
        ];
    }

    /**
     * @dataProvider casesGetQueryFromUrl
     */
    public function testCasesGetQueryFromUrl(array $expected, string $url): void
    {
        static::assertSame($expected, Utils::getQueryFromUrl($url));
    }
}
