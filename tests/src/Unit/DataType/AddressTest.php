<?php

namespace Cheppers\OtpspClient\Tests\Unit\DataType;

use Cheppers\OtpspClient\DataType\Address;
use PHPUnit\Framework\TestCase;
use function Sodium\add;

/**
 * @covers \Cheppers\OtpspClient\DataType\Address<extended>
 */
class AddressTest extends BaseTestBase
{
    protected $className = Address::class;

    public function casesSetState()
    {
        return [
            'empty' => [new Address(), []],
            'basic' => [
                $this->getBaseAddress(),
                [
                    'name'     => 'test-name',
                    'company'  => 'test-company',
                    'phone'    => 'test-phone',
                    'country'  => 'test-country',
                    'state'    => 'test-state',
                    'zip'      => 'test-zip',
                    'city'     => 'test-city',
                    'address'  => 'test-address',
                    'address2' => 'test-address-2',
                ],
            ],
        ];
    }

    public function casesExportData(): array
    {
        return [
            'empty' => [
                [
                    'name'     => '',
                    'country'  => '',
                    'state'    => '',
                    'city'     => '',
                    'zip'      => '',
                    'address'  => '',
                    'address2' => '',
                ],
                new Address(),
            ],
            'basic' => [
                [
                    'name'     => 'test-name',
                    'company'  => 'test-company',
                    'country'  => 'test-country',
                    'state'    => 'test-state',
                    'city'     => 'test-city',
                    'zip'      => 'test-zip',
                    'address'  => 'test-address',
                    'address2' => 'test-address-2',
                    'phone'    => 'test-phone',
                ],
                $this->getBaseAddress(),
            ],
        ];
    }

    protected function getBaseAddress(): Address
    {
        $address = new Address();
        $address->name = 'test-name';
        $address->company = 'test-company';
        $address->phone = 'test-phone';
        $address->country = 'test-country';
        $address->state = 'test-state';
        $address->zip = 'test-zip';
        $address->city = 'test-city';
        $address->address = 'test-address';
        $address->address2 = 'test-address-2';

        return $address;
    }
}
