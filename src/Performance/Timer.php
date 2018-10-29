<?php

declare(strict_types=1);

namespace Benchmark\Performance;

use Benchmark\Performance\Output\ {
    Formatter, Html, Shell
};

/**
 * allows to check performance of framework
 *
 * @category    BlueTree Service Benchmark
 * @package     Benchmark
 * @subpackage  Performance
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree-service
 */
class Timer
{
    /**
     * information that marker time goes to group
     * @var array
     */
    protected static $groupOn = [];

    /**
     * contains array of times for group of markers
     * @var array
     */
    protected static $group = [];

    /**
     * contains data about benchmark started memory usage
     * @var integer
     */
    protected static $sessionMemoryStart = 0;

    /**
     * contains data about started benchmark session time
     * @var integer
     */
    protected static $sessionBenchmarkStart = 0;

    /**
     * contains data about benchmark marker time
     * @var integer
     */
    protected static $sessionBenchmarkMarker = 0;

    /**
     * contains data about benchmark markers
     * @var array
     */
    protected static $sessionBenchmarkMarkers = [];

    /**
     * contains data about benchmark finish time
     * @var integer
     */
    protected static $sessionBenchmarkFinish = 0;

    /**
     * contains information that benchmark is on or off
     * @var bool
     */
    protected static $sessionBenchmarkOn = true;

    /**
     * contains color information for group of markers
     * @var int
     */
    protected static $backgroundColor = 0x202020;

    /**
     * start benchmark, and set in internal session start time
     *
     * @param boolean $enabled
     */
    public static function start(bool $enabled = true) : void
    {
        if ($enabled) {
            $time = microtime(true);
            self::$sessionMemoryStart = memory_get_usage(true);
            self::$sessionBenchmarkStart = $time;
            self::$sessionBenchmarkMarker = $time;
        } else {
            self::$sessionBenchmarkOn = false;
        }
    }

    /**
     * set marker and set in session time of run up to current position
     *
     * @param string $name name of marker
     */
    public static function setMarker(string $name) : void
    {
        if (self::$sessionBenchmarkOn) {
            $markerTime = microtime(true) - self::$sessionBenchmarkMarker;
            $markerColor = self::$backgroundColor;

            if (!empty(self::$groupOn)) {
                foreach (array_keys(self::$group) as $marker) {
                    if (!isset(self::$group[$marker]['time'])) {
                        $groupMarkerTime = $markerTime;
                    } else {
                        $groupMarkerTime = self::$group[$marker]['time'] + $markerTime;
                    }

                    self::$group[$marker]['time'] = $groupMarkerTime;
                }
            }

            self::$sessionBenchmarkMarker = microtime(true);
            self::$sessionBenchmarkMarkers[] = array(
                'marker_name' => $name,
                'marker_time' => $markerTime,
                'marker_memory' => memory_get_usage(true),
                'marker_color' => dechex($markerColor)
            );
        }
    }

    /**
     * start group of markers
     *
     * @param string $groupName
     */
    public static function startGroup(string $groupName) : void
    {
        if (self::$sessionBenchmarkOn) {
            self::$backgroundColor += 0x101010;

            self::$sessionBenchmarkMarkers[] = array(
                'marker_name' => $groupName . ' START',
                'marker_time' => '',
                'marker_memory' => '',
                'marker_color' => dechex(self::$backgroundColor)
            );

            self::$group[$groupName]['memory'] = memory_get_usage(true);
            self::$groupOn[$groupName] = $groupName;
        }
    }

    /**
     * end counting given group of markers
     * @param string $groupName
     */
    public static function endGroup(string $groupName) : void
    {
        if (self::$sessionBenchmarkOn) {
            unset(self::$groupOn[$groupName]);
            $memoryUsage = memory_get_usage(true) - self::$group[$groupName]['memory'];

            self::$sessionBenchmarkMarkers[] = array(
                'marker_name' => $groupName . ' END',
                'marker_time' => self::$group[$groupName]['time'],
                'marker_memory' => $memoryUsage,
                'marker_color' => dechex(self::$backgroundColor)
            );

            self::$backgroundColor -= 0x101010;
        }
    }

    /**
     * stop benchmark, and time counting, save last run time
     */
    public static function stop() : void
    {
        if (self::$sessionBenchmarkOn) {
            self::$sessionBenchmarkFinish = microtime(true);
        }
    }

    /**
     * prepare view and display list of markers, their times and percentage values
     *
     * @param callable|null $dataFormatter
     * @return array
     * @todo data formatter as array with callable, to avoid write all formatter
     */
    public static function calculateStats(?callable $dataFormatter = null) : array
    {
        $aggregation = [];
        $dataFormatter = $dataFormatter ?? [Formatter::class, 'rawValues'];

        if (self::$sessionBenchmarkOn) {
            $benchmarkStartTime = self::$sessionBenchmarkStart;
            $benchmarkEndTime = self::$sessionBenchmarkFinish;
            $totalTime = $benchmarkEndTime - $benchmarkStartTime;
            $formatTime = $dataFormatter($totalTime, 'total_rune_time');
            $memoryUsage = $dataFormatter(memory_get_usage(), 'total_memory');

            $aggregation = [
                'total_rune_time' => $formatTime,
                'total_memory' => $memoryUsage,
            ];

            foreach (self::$sessionBenchmarkMarkers as $marker) {
                if ($marker['marker_time'] === '') {
                    $time = '-';
                    $percent = '-';
                    $ram = '-';
                } else {
                    $ram = ($marker['marker_memory'] - self::$sessionMemoryStart);
                    $ram = $dataFormatter($ram, 'memory');

                    $percent = ($marker['marker_time'] / $totalTime) * 100;
                    $percent = $dataFormatter($percent, 'percentage');

                    $time = $dataFormatter($marker['marker_time'], 'time');
                }

                $aggregation['markers'][] = [
                    'color' => $marker['marker_color'],
                    'name' => $marker['marker_name'],
                    'time' => $time,
                    'percentage' => $percent,
                    'memory' => $ram,
                ];
            }
        }

        return $aggregation;
    }

    /**
     * @param array $stats
     * @param string $path
     * @throws \DomainException
     */
    public static function toFile(array $stats, string $path) : void
    {
        if (!file_exists($path)) {
            throw new \DomainException('File don\'t exists: ' . $path);
        }

        file_put_contents($path, json_encode($stats));
    }

    /**
     * turn off benchmark
     */
    public static function turnOffBenchmark() : void
    {
        self::$sessionBenchmarkOn = false;
    }

    /**
     * turn on benchmark
     */
    public static function turnOnBenchmark() : void
    {
        self::$sessionBenchmarkOn = true;
    }

    /**
     * @return float
     */
    public static function getCurrentTime() : float
    {
        return microtime(true) - self::$sessionBenchmarkMarker;
    }

    /**
     * @param string $type
     * @return array|string
     */
    public static function getFormattedOutput($type = 'raw')
    {
        switch ($type) {
            case 'shell':
                return (new Shell)->formatOutput(self::calculateStats([Formatter::class, 'formatValues']));

            case 'html':
                return (new Html)->formatOutput(self::calculateStats([Formatter::class, 'formatValues']));

            case 'raw+':
                return self::calculateStats([Formatter::class, 'formatValues']);

            default:
                return self::calculateStats();
        }
    }
}
