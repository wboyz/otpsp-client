<?php

declare(strict_types = 1);

namespace Cheppers\OtpspClient;

class Utils
{

    public static function getQueryFromUrl(string $url)
    {
        $query = parse_url($url, PHP_URL_QUERY);
        if (!$query) {
            return null;
        }

        $values =[];
        parse_str($query, $values);

        return $values;
    }
}
