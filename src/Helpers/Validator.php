<?php

namespace capsweb\Helpers;

class Validator
{
    public static function required(string $value): bool
    {
        return trim($value) !== '';
    }

    public static function maxLength(string $value, int $max): bool
    {
        return mb_strlen($value) <= $max;
    }

    public static function numeric($value): bool
    {
        return is_numeric($value) && $value >= 0;
    }

    public static function email(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function sanitizeString(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    public static function int($value): int
    {
        return filter_var($value, FILTER_VALIDATE_INT) ?: 0;
    }

    public static function float($value): float
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) ?: 0.0;
    }
}
