<?php

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\Product;
use Cheppers\OtpspClient\DataType\Redirect;

/**
 * @covers \Cheppers\OtpspClient\DataType\Redirect<extended>
 */
class RedirectTest extends RedirectBaseTestBase
{

    /**
     * {@inheritdoc}
     */
    protected $className = Redirect::class;

    /**
     * {@inheritdoc}
     */
    public function casesExportData()
    {
        return [
            'empty' => [[], []],
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
                [
                    'merchantId' => 'PUBLICTESTHUF',
                    'customerEmail' => 'example@example.com',
                    'products' => [
                        new Product(),
                    ],
                    'langCode' => 'HU',
                    'backrefUrl' => 'http://backref.exmaple.com',
                    'timeoutUrl' => 'http://timeout.exmaple.com',
                ],
            ],
        ];
    }
}
