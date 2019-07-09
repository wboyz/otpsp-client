<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\Order;
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
                        'ORDER_PNAME[]' => [
                            'foo'
                        ],
                        'ORDER_PCODE[]' => [
                            '1111'
                        ],
                    ],
                    'langCode' => 'HU',
                    'backrefUrl' => 'http://backref.exmaple.com',
                    'timeoutUrl' => 'http://timeout.exmaple.com',
                    'order' => [],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function casesGetHashValues()
    {
        return [
            'empty' => [[], [], new Redirect()],
            'basic' => [
                [
                    'PUBLICTESTHUF',
                    'Foo',
                    '2000-01-01 00:00:00',
                    'EUR',
                    999.9,
                    'Product 01',
                    'mzCode',
                    'Bar',
                    99.9,
                    1,
                    5,
                    27,
                ],
                [
                    [
                        'MERCHANT' => 'PUBLICTESTHUF',
                    ],
                    [
                        'ORDER_REF' => 'Foo',
                    ],
                    [
                        'ORDER_DATE' => '2000-01-01 00:00:00',
                    ],
                    [
                        'PRICES_CURRENCY' => 'EUR',
                    ],
                    [
                        'ORDER_SHIPPING' => 999.9,
                    ],
                    [
                        'ORDER_PNAME[]' => 'Product 01',
                    ],
                    [
                        'ORDER_PCODE[]' => 'mzCode',
                    ],
                    [
                        'ORDER_PINFO[]' => 'Bar',
                    ],
                    [
                        'ORDER_PRICE[]' => 99.9,
                    ],
                    [
                        'ORDER_QTY[]' => 1,
                    ],
                    [
                        'DISCOUNT[]' => 5,
                    ],
                    [
                        'ORDER_VAT[]' => 27,
                    ],
                    [
                        'BILL_EMAIL' => 'example@example.com',
                    ],
                ],
                new Redirect(),
            ]
        ];
    }

    /**
     * @dataProvider casesGetHashValues
     */
    public function testGetHashValues($expected, array $data, Redirect $redirect)
    {
        self::assertSame($expected, $redirect->getHashValues($data));
    }
}
