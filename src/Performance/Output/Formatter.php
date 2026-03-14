<?php

declare(strict_types=1);

namespace Benchmark\Performance\Output;

class Formatter
{
    /**
     * @param string $type
     * @param mixed $value
     * @return mixed $value
     */
    public static function formatValues(mixed $value, string $type): mixed
    {
        return match ($type) {
            'time', 'total_rune_time' => self::formatTime($value),
            'total_memory' => '~' . ($value / 1024) . ' kB',
            'percentage' => \number_format($value, 5) . ' %',
            'memory' => '~' . \number_format($value, 3, ',', '') . ' kB',
            default => $value,
        };
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public static function rawValues($value): mixed
    {
        return $value;
    }

    /**
     * @param float $value
     * @return string
     */
    protected static function formatTime(float $value) : string
    {
        return '~' . \number_format($value * 1000, 4, '.', ' ') . ' ms';
    }
}
