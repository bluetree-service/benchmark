<?php

namespace Benchmark\Performance\Output;

class Formatter
{
    /**
     * @param string $type
     * @return string|mixed $ value
     * @param mixed $value
     */
    public function formatValues($value, string $type)
    {
        switch ($type) {
            case 'total_rune_time':
                return self::formatTime($value);

            case 'total_memory':
                return '~' . ($value / 1024) . ' kB';

            case 'percentage':
                return number_format($value, 5) . ' %';

            case 'memory':
                return '~' . number_format($value, 3, ',', '') . ' kB';

            case 'time':
                return self::formatTime($value);

            default:
                return $value;
        }
    }

    /**
     * @param mixed|string $value
     * @return mixed
     */
    public function rawValues($value)
    {
        return $value;
    }

    /**
     * @param $value
     * @return string
     */
    protected static function formatTime(float $value) : string
    {
        return '~' . number_format($value * 1000, 4, '.', ' ') . ' ms';
    }
}
