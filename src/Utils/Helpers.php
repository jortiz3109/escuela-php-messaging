<?php

namespace E4\Messaging\Utils;

class Helpers
{
    public static function getMissingKeys(array $original, array $compare)
    {
        $output = [];
        foreach ($original as $key => $value) {
            if (!array_key_exists($key, $compare)) {
                $output[$key] = $value;
            } elseif (is_array($value) || is_array($compare[$key])) {
                $match = self::getMissingKeys($value, $compare[$key]);
                if (count($match) > 0) {
                    $output[$key] = $match;
                }
            }
        }
        return $output;
    }
}
