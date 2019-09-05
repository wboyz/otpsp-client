<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\Urls;

/**
 * @covers \Cheppers\OtpspClient\DataType\Urls<extended>
 */
class UrlsTest extends BaseTestBase
{
    /**
     * {@inheritdoc}
     */
    protected $className = Urls::class;

    public function casesSetState()
    {
        return [
            'empty' => [new Urls(), []],
            'basic' => [
                $this->getBaseUrls(),
                [
                    'timeout' => 'test-timeout',
                    'fail'    => 'test-fail',
                    'cancel'  => 'test-cancel',
                    'success' => 'test-success',
                ],
            ],
        ];
    }

    public function casesExportData(): array
    {
        return [
            'empty' => [
                [
                    'success' => '',
                    'fail'    => '',
                    'cancel'  => '',
                    'timeout' => '',
                ],
                new Urls(),
            ],
            'basic' => [
                [
                    'success' => 'test-success',
                    'fail'    => 'test-fail',
                    'cancel'  => 'test-cancel',
                    'timeout' => 'test-timeout',
                ],
                $this->getBaseUrls(),
            ]
        ];
    }

    protected function getBaseUrls(): Urls
    {
        $urls = new Urls();
        $urls->timeout = 'test-timeout';
        $urls->fail = 'test-fail';
        $urls->cancel = 'test-cancel';
        $urls->success = 'test-success';

        return $urls;
    }
}
