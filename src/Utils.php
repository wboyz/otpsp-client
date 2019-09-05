<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient;

class Utils
{

    public static function getQueryFromUrl(string $url): array
    {
        $query = parse_url($url, PHP_URL_QUERY);
        if (!$query) {
            return [];
        }

        $values = [];
        parse_str($query, $values);

        return $values;
    }
}
