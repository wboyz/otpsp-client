<?php

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\Product;

/**
 * @covers \Cheppers\OtpspClient\DataType\Product<extended>
 */
class ProductTest extends RedirectBaseTestBase
{

    /**
     * @var string
     */
    protected $className = Product::class;

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
                        'ORDER_PNAME[]' => 'Foo',
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
                ],
                [
                    'sku' => 'mzCode',
                    'productName' => 'Foo',
                    'quantity' => 1,
                    'price' => 99.9,
                    'description' => 'Bar',
                    'discount' => 5,
                    'vat' => 27,
                ],
            ],
        ];
    }
}
