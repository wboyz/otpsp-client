<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\Item;

/**
 * @covers \Cheppers\OtpspClient\DataType\Item<extended>
 */
class ItemTest extends BaseTestBase
{
    /**
     * {@inheritdoc}
     */
    protected $className = Item::class;

    public function casesSetState()
    {
        return [
            'empty' => [new Item(), []],
            'basic' => [
                $this->getBaseItem(),
                [
                    'amount'      => 42,
                    'description' => 'test-description',
                    'price'       => 43.5,
                    'ref'         => 'test-ref',
                    'tax'         => 45,
                    'title'       => 'test-title',
                ],
            ],
        ];
    }

    public function casesExportData(): array
    {
        return [
            'empty' => [
                [
                    'ref'         => '',
                    'title'       => '',
                    'description' => '',
                    'amount'      => 0,
                    'price'       => 0.0,
                    'tax'         => 0,
                ],
                new Item(),
            ],
            'basic' => [
                [
                    'ref'         => 'test-ref',
                    'title'       => 'test-title',
                    'description' => 'test-description',
                    'amount'      => 42,
                    'price'       => 43.5,
                    'tax'         => 45,
                ],
                $this->getBaseItem(),
            ],
        ];
    }

    protected function getBaseItem(): Item
    {
        $item = new Item();
        $item->amount = 42;
        $item->description = 'test-description';
        $item->price = 43.5;
        $item->ref = 'test-ref';
        $item->tax = 45;
        $item->title = 'test-title';

        return $item;
    }
}
