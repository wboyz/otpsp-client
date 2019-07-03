<?php

declare(strict_types=1);

namespace Cheppers\OtpspClient;

class Checksum
{
    /**
     * @var string
     */
    public $hashAlgorithm = 'md5';

    public function calculate(array $data, string $secretKey)
    {
        if (empty($data)) {
            return '';
        }

        $hashString = '';

        foreach ($data as $field) {
            if (is_array($field)) {
                throw new \Exception('Data can not be multidimensional array.');
            }

            $hashString .= strlen(stripslashes($field)) . $field;
        }

        return hash_hmac($this->hashAlgorithm, $hashString, trim($secretKey));
    }
}
