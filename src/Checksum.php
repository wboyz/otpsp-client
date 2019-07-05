<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient;

class Checksum
{

    /**
     * @var string
     */
    public $hashAlgorithm = 'md5';

    public function calculate(array $data, string $secretKey): string
    {
        if (empty($data)) {
            return '';
        }

        $hashString = '';

        foreach ($data as $field) {
            if (is_array($field)) {
                throw new \InvalidArgumentException('Data can not be multidimensional array.', 1);
            }

            $hashString .= strlen(stripslashes($field)) . $field;
        }

        return hash_hmac($this->hashAlgorithm, $hashString, trim($secretKey));
    }
}
