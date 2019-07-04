<?php

namespace Cheppers\OtpspClient\Tests\Unit;

use Cheppers\OtpspClient\UrlParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Cheppers\OtpspClient\UrlParser
 */
class UrlParserTest extends TestCase
{
    public function casesGetUrlQueryVariable()
    {
        return [
            'key exists in url' => [
                'bar',
                'http://example.com?foo=bar&bar=foo',
                'foo',
            ],
        ];
    }

    /**
     * @dataProvider casesGetUrlQueryVariable
     */
    public function testGetUrlQueryVariable(string $expected, string $url, string $key)
    {
        $actual = (new UrlParser())->getUrlQueryVariable($url, $key);

        static::assertSame($expected, $actual);
    }
}
