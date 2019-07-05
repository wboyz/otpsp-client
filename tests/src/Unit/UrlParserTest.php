<?php

declare(strict_types = 1);

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
            'key not exists in url' => [
                null,
                'http://example.com?foo=bar&bar=foo',
                'baz',
            ],
            'no query in url' => [
                null,
                'http://example.com',
                'baz',
            ],
        ];
    }

    /**
     * @dataProvider casesGetUrlQueryVariable
     */
    public function testGetUrlQueryVariable(?string $expected, string $url, string $key)
    {
        $actual = (new UrlParser())->getUrlQueryVariable($url, $key);

        static::assertSame($expected, $actual);
    }

    public function casesRemoveQueryVariable()
    {
        return [
            'key exists in url' => [
                'http://example.com?bar=foo',
                'http://example.com?foo=bar&bar=foo',
                'foo',
            ],
            'key not exists in url' => [
                'http://example.com?foo=bar&bar=foo',
                'http://example.com?foo=bar&bar=foo',
                'baz',
            ],
        ];
    }

    /**
     * @dataProvider casesRemoveQueryVariable
     */
    public function testRemoveQueryVariable(string $expected, string $url, string $key)
    {
        $actual = (new UrlParser())->removeQueryVariable($url, $key);

        static::assertSame($expected, $actual);
    }

    public function casesBuildUrl()
    {
        return [
            'minimal' => [
                'http://hostname',
                [
                    'host' => 'hostname',
                ],
            ],
            'full test' => [
                'http://username:password@hostname:9090/path?arg=value#anchor',
                [
                    'scheme' => 'http',
                    'user' => 'username',
                    'pass' => 'password',
                    'host' => 'hostname',
                    'port' => '9090',
                    'path' => 'path',
                    'query' => 'arg=value',
                    'fragment' => 'anchor',
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesBuildUrl
     */
    public function testBuildUrl(string $expected, array $parts)
    {
        $actual = (new UrlParser())->buildUrl($parts);

        static::assertSame($expected, (new UrlParser())->buildUrl($parts));
    }
}
