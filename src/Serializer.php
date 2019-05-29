<?php

declare(strict_types=1);

namespace Cheppers\OtpClient;

class Serializer extends UrlParser
{
    public $hashAlgorithm = 'md5';
    public $queryVariable = 'ctrl';

    public function encode(array $data, string $secretKey)
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

    public function decode(string $url, string $secretKey)
    {
        if ($url === '') {
            return '';
        }

        $urlWithNoCtrl = $this->removeQueryVariable($url, $this->queryVariable);
        $hashString = strlen($urlWithNoCtrl) . $urlWithNoCtrl;

        return hash_hmac($this->hashAlgorithm, $hashString, trim($secretKey));
    }

    public function isUrlValid(string $url, string $secretKey): bool
    {
        return $this->decode($url, $secretKey)
        === $this->getUrlQueryVariable($url, $this->queryVariable)
        ? true
        : false;
    }
}
