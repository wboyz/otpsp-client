<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient;

class Checksum implements ChecksumInterface
{

    /**
     * @var string
     */
    protected $hashAlgorithm = 'sha384';

    /**
     * {@inheritdoc}
     */
    public function calculate(string $secretKey, string $data): string
    {
        if ($data === '') {
            return '';
        }

        return base64_encode(hash_hmac($this->hashAlgorithm, $data, trim($secretKey), true));
    }

    /**
     * {@inheritdoc}
     */
    public function verify(string $secretKey, string $data, string $checksum): bool
    {
        return $checksum === $this->calculate($secretKey, $data);
    }
}
