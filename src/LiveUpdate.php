<?php

declare(strict_types=1);

namespace Cheppers\OtpClient;

class LiveUpdate
{
    public $formData;
    public $baseUrl = 'https://sandbox.simplepay.hu';
    public $targetUrl = '/payment/order/lu.php';

    public function createForm(
        string $formName,
        string $submitElement,
        string $submitElementText
    ): string {
        $form = "\n<form action='"
            . $this->baseUrl
            . $this->targetUrl .
            "' method='POST' id='"
            . $formName
            . "' accept-charset='UTF-8'>";
        foreach ($this->formData as $name => $field) {
            if (is_array($field)) {
                foreach ($field as $subField) {
                    $form .= $this->createHiddenField($name . "[]", $subField);
                }
                continue;
            }
            if ($name == "BACK_REF" || $name == "TIMEOUT_URL") {
                $concat = '?';
                if (strpos($field, '?') !== false) {
                    $concat = '&';
                }
                $field .= $concat . 'order_ref=' . $this->fieldData['ORDER_REF']
                       . '&order_currency=' . $this->fieldData['PRICES_CURRENCY'];
                $field = $this->protocol . '://' . $field;
            }
            $form .= $this->createHiddenField($name, $field);
        }
        $form .= $this->formSubmitElement($formName, $submitElement, $submitElementText);
        $form .= "\n</form>";
        return $form;
    }

    public function createHiddenField(string $name, string $value)
    {
        $inputId = substr($name, -2, 2) == "[]"
            ? substr($name, 0, -2)
            : $name;
        return "\n<input type='hidden' name='" . $name . "' id='" . $inputId . "' value='" . $value . "' />";
    }

    public function formSubmitElement(
        string $formName,
        string $submitElement,
        string $submitElementText
    ): string {
        switch ($submitElement) {
            case 'link':
                $element = "\n<a href='javascript:document.getElementById(\"" . $formName ."\").submit()'>"
                         . addslashes($submitElementText) . "</a>";
                break;
            case 'auto':
                $element = "\n<button type='submit'>" . addslashes($submitElementText) . "</button>";
                $element .= "\n<script language=\"javascript\" type=\"text/javascript\">"
                         . "document.getElementById(\"" . $formName . "\").submit();</script>";
                break;
            default:
                $element = "\n<button type='submit'>" . addslashes($submitElementText) . "</button>";
                break;
        }

        return $element;
    }
}
