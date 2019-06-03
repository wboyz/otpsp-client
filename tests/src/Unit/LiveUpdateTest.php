<?php

declare(strict_types=1);

namespace Cheppers\OtpClient\Tests\Unit;

use Cheppers\OtpClient\LiveUpdate;
use PHPUnit\Framework\TestCase;

/**
 * Class SerializerTest
 * @covers \Cheppers\OtpClient\LiveUpdate
 * @package Cheppers\OtpClient\Tests\Unit
 */
class LiveUpdateTest extends TestCase
{
    public function casesCreateForm(): array
    {
        return [
            'Empty form data test' => [
                'myFormName',
                'button',
                'mySubmitElementText',
                [],
                "\n<form action='https://sandbox.simplepay.hu/payment/order/lu.php' "
                . "method='POST' id='myFormName' accept-charset='UTF-8'>"
                . "\n<button type='submit'>mySubmitElementText</button>"
                . "\n</form>",
            ],
            'Array with more value form data test' => [
                'myFormName',
                'button',
                'mySubmitElementText',
                [
                    'foo' => [
                        'test' => 'bar',
                    ],
                    'bak' => [
                        'test' => 'baz',
                    ],
                ],
                "\n<form action='https://sandbox.simplepay.hu/payment/order/lu.php' "
                . "method='POST' id='myFormName' accept-charset='UTF-8'>"
                . "\n<input type='hidden' name='foo[]' id='foo' value='bar' />"
                . "\n<input type='hidden' name='bak[]' id='bak' value='baz' />"
                . "\n<button type='submit'>mySubmitElementText</button>"
                . "\n</form>",
            ],
        ];
    }

    /**
     * @dataProvider casesCreateForm
     */
    public function testCreateForm(
        string $formName,
        string $submitElement,
        string $submitElementText,
        array $formData,
        string $expected
    ): void {
        $liveUpdate = new LiveUpdate();
        $liveUpdate->formData =$formData;

        $actual = $liveUpdate->createForm($formName, $submitElement, $submitElementText);
        static::assertSame($expected, $actual);
    }

    public function casesCreateHiddenField(): array
    {
        return [
            '[] in name given test' => [
                'foo[]',
                'bar',
                "\n<input type='hidden' name='foo[]' id='foo' value='bar' />",
            ],
            'no [] in name given test' => [
                'foo',
                'bar',
                "\n<input type='hidden' name='foo' id='foo' value='bar' />",
            ],
        ];
    }

    /**
     * @dataProvider casesCreateHiddenField
     */
    public function testCreateHiddenField(
        string $name,
        string $value,
        string $expected
    ): void {
        $liveUpdate = new LiveUpdate();

        $actual = $liveUpdate->createHiddenField($name, $value);
        static::assertSame($expected, $actual);
    }

    public function casesFormSubmitElement(): array
    {
        return [
            'link test' => [
                'myFormName',
                'link',
                'myElementText',
                "\n<a href='javascript:document.getElementById(\"myFormName\").submit()'>myElementText</a>"
            ],
            'auto test' => [
                'myFormName',
                'auto',
                'myElementText',
                "\n<button type='submit'>myElementText</button>"
                . "\n<script language=\"javascript\" type=\"text/javascript\">"
                . "document.getElementById(\"myFormName\").submit();</script>",
            ],
            'default test' => [
                'myFormName',
                'button',
                'myElementText',
                "\n<button type='submit'>myElementText</button>",
            ],
        ];
    }

    /**
     * @dataProvider casesFormSubmitElement
     */
    public function testFormSubmitElement(
        string $formName,
        string $submitElement,
        string $submitElementText,
        string $expected
    ): void {
        $liveUpdate = new LiveUpdate();
        $actual = $liveUpdate->formSubmitElement($formName, $submitElement, $submitElementText);

        static::assertSame($expected, $actual);
    }
}
