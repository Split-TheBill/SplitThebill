<?php

namespace App\Support;

final class PhoneNumber
{
    public static function normalize(mixed $value): mixed
    {
        if (! is_string($value) && ! is_int($value)) {
            return $value;
        }

        $digits = preg_replace('/\D+/', '', trim((string) $value)) ?? '';

        if (str_starts_with($digits, '620')) {
            $digits = '62'.substr($digits, 3);
        } elseif (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        return $digits === '' ? '' : '+'.$digits;
    }
}
