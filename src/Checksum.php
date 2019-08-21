<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient;

use InvalidArgumentException;

class Checksum
{
    protected $hashAlgorithm = 'sha384';

    public function calculate(string $data, string $secretKey): string
    {
        if (empty($data)) {
            return '';
        }

        return base64_encode(hash_hmac($this->hashAlgorithm, $data, trim($secretKey), true));
    }
}
