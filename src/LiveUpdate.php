<?php

declare(strict_types=1);

namespace Cheppers\OtpClient;

class LiveUpdate
{
    public $formData;
    public $baseUrl = 'https://sandbox.simplepay.hu';
    public $targetUrl = '/payment/order/lu.php';

    public function createForm(string $formName)
    {
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
        }
    }

    public function createHiddenField(string $name, string $value)
    {
        $inputId = substr($name, -2, 2) == "[]"
            ? substr($name, 0, -2)
            : $name;
        return "\n<input type='hidden' name='" . $name . "' id='" . $inputId . "' value='" . $value . "' />";
    }
}
