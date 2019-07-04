<?php


namespace Cheppers\OtpspClient;


class Utils
{

    public static function flatArray(array $array = [], array $skip = []): array
    {
        if (count($array) === 0) {
            return [];
        }

        $return = [];
        foreach ($array as $name => $item) {
            if (in_array($name, $skip)) {
                continue;
            }

            if (is_array($item)) {
                foreach ($item as $subItem) {
                    $return[] = $subItem;
                }

                continue;
            }

            $return[] = $item;
        }

        return $return;
    }
}
