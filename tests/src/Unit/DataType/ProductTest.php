<?php

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\Product;

class ProductTest extends \PHPUnit\Framework\TestCase
{
    public function casesExportData()
    {
        $p1 = new Product();
        $p1->sku = 'mzCode';
        $p1->productName = 'Foo';
        $p1->quantity = 1;
        $p1->price = 99.9;
        $p1->description = 'Bar';
        $p1->discount = 5;
        $p1->vat = 27;

        return [
            'empty' => [[], new Product()],
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
                $p1,
            ],
        ];
    }

    /**
     * @dataProvider casesExportData
     */
    public function testExportData(array $expected, Product $product)
    {
        static::assertSame($expected, $product->exportData());
    }
}
