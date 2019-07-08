<?php

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\Product;
use Cheppers\OtpspClient\DataType\Redirect;
use PHPUnit\Framework\TestCase;

class RedirectTest extends TestCase
{
    public function casesExportData()
    {
        $redirect = new Redirect();
        $redirect->merchantId = 'PUBLICTESTHUF';
        $redirect->customerEmail = 'example@example.com';
        $redirect->products[] = new Product();
        $redirect->langCode = 'HU';
        $redirect->backrefUrl = 'http://backref.exmaple.com';
        $redirect->timeoutUrl = 'http://timeout.exmaple.com';

        return [
            'empty' => [[], new Redirect()],
            'basic' => [
                [
                    [
                        'MERCHANT' => 'PUBLICTESTHUF',
                    ],
                    [
                        'BILL_EMAIL' => 'example@example.com',
                    ],
                    [
                        'TIMEOUT_URL' => 'http://timeout.exmaple.com',
                    ],
                    [
                        'BACK_REF' => 'http://backref.exmaple.com',
                    ],
                    [
                        'LANGUAGE' => 'HU',
                    ],
                ],
                $redirect,
            ],
        ];
    }

    /**
     * @dataProvider casesExportData
     */
    public function testExportData($expected, Redirect $redirect)
    {
        static::assertSame($expected, $redirect->exportData());
    }
}
