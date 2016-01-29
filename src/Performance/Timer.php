<?php

namespace Benchmark\Performance;

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
    protected static $backgroundColor = 0x3d3d3d;

    /**
     * start benchmark, and set in internal session start time
     *
     * @param boolean $enabled
     */
    public static function start($enabled = true)
    {
        if ($enabled) {
            $time                          = microtime(true);
            self::$sessionMemoryStart      = memory_get_usage();
            self::$sessionBenchmarkStart   = $time;
            self::$sessionBenchmarkMarker  = $time;
        } else {
            self::$sessionBenchmarkOn      = false;
        }
    }

    /**
     * set marker and set in session time of run up to current position
     *
     * @param string $name name of marker
     */
    public static function setMarker($name)
    {
        if (self::$sessionBenchmarkOn) {
            $markerTime = microtime(true) - self::$sessionBenchmarkMarker;
            $markerColor = false;

            if (!empty(self::$groupOn)) {
                $markerColor = self::$backgroundColor;

                foreach (array_keys(self::$group) as $marker) {
                    if (!isset(self::$group[$marker]['time'])) {
                        $groupMarkerTime = $markerTime;
                    } else {
                        $groupMarkerTime = self::$group[$marker]['time'] + $markerTime;
                    }

                    self::$group[$marker]['time'] = $groupMarkerTime;
                }
            }

            self::$sessionBenchmarkMarker    = microtime(true);
            self::$sessionBenchmarkMarkers[] = array(
                'marker_name'       => $name,
                'marker_time'       => $markerTime,
                'marker_memory'     => memory_get_usage(),
                'marker_color'      => $markerColor
            );
        }
    }

    /**
     * start group of markers
     *
     * @param string $groupName
     */
    public static function startGroup($groupName)
    {
        if (self::$sessionBenchmarkOn) {
            self::$backgroundColor += 0x101010;

            self::$sessionBenchmarkMarkers[] = array(
                'marker_name'       => $groupName . ' START',
                'marker_time'       => '',
                'marker_memory'     => '',
                'marker_color'      => self::$backgroundColor
            );

            self::$group[$groupName]['memory'] = memory_get_usage();
            self::$groupOn[$groupName]         = $groupName;
        }
    }

    /**
     * end counting given group of markers
     * @param string $groupName
     * @uses Test_Benchmark::$benchmarkOn
     * @uses Test_Benchmark::$backgroundColor
     * @uses Test_Benchmark::$session
     * @uses Test_Benchmark::$group
     * @uses Test_Benchmark::$groupOn
     */
    public static function endGroup($groupName)
    {
        if (self::$sessionBenchmarkOn) {
            unset(self::$groupOn[$groupName]);
            $memoryUsage = memory_get_usage() - self::$group[$groupName]['memory'];

            self::$sessionBenchmarkMarkers[] = array(
                'marker_name'       => $groupName . ' END',
                'marker_time'       => self::$group[$groupName]['time'],
                'marker_memory'     => $memoryUsage,
                'marker_color'      => self::$backgroundColor
            );

            self::$backgroundColor -= 0x101010;
        }
    }

    /**
     * stop benchmark, and time counting, save last run time
     */
    public static function stop()
    {
        if (self::$sessionBenchmarkOn) {
            self::$sessionBenchmarkFinish = microtime(true);
        }
    }

    /**
     * prepare view and display list of markers, their times and percentage values
     */
    public static function calculateStats()
    {
        $display = '';

        if (self::$sessionBenchmarkOn) {
            $display = '<div style="
            color: #FFFFFF;
            background-color: #3d3d3d;
            border: 1px solid #FFFFFF;
            width: 90%;
            text-align: center;
            margin: 25px auto;
            ">';

            $benchmarkStartTime = self::$sessionBenchmarkStart;
            $benchmarkEndTime   = self::$sessionBenchmarkFinish;
            $total              = ($benchmarkEndTime - $benchmarkStartTime) *1000;
            $formatTime         = number_format($total, 5, '.', ' ');
            $memoryUsage        = memory_get_usage()/1024;
            $display .= '
                Total application runtime: ' . $formatTime . ' ms&nbsp;&nbsp;&nbsp;&nbsp;
                Total memory usage: '. number_format($memoryUsage, 3, ',', '')
                . ' kB<br /><br />';
            $display .= 'Marker times:<br /><table style="width:100%">'."\n";

            foreach (self::$sessionBenchmarkMarkers as $marker) {
                if ($marker['marker_color']) {
                    $additionalColor = 'background-color:#' . dechex($marker['marker_color']);
                } else {
                    $additionalColor = '';
                }

                if ($marker['marker_time'] === '') {
                    $time       = '-';
                    $percent    = '-';
                    $ram        = '-';
                } else {
                    $ram      = ($marker['marker_memory'] - self::$sessionMemoryStart) / 1024;
                    $ram      = number_format($ram, 3, ',', '');
                    $percent  = ($marker['marker_time'] / $total) *100000;
                    $percent  = number_format($percent, 5);
                    $time     = number_format(
                        $marker['marker_time'] *1000,
                        5,
                        '.',
                        ' '
                    );
                    $time       .= ' ms';
                    $percent    .= ' %';
                    $ram        .= ' kB';
                }

                $display .= '<tr style="' . $additionalColor . '">
                    <td style="width:40%;color:#fff">' . $marker['marker_name'] . '</td>' . "\n";
                $display .= '<td style="width:20%;color: #fff;">' . $time . '</td>'."\n";
                $display .= '<td style="width:20%;color: #fff;">' . $percent . '</td>'."\n";
                $display .= '<td style="width:20%;color:#fff">' . $ram . '</td>
                    </tr>' . "\n";
            }

            $display .= '</table></div>';
        }

        return $display;
    }

    /**
     * turn off benchmark
     */
    public static function turnOffBenchmark()
    {
        self::$sessionBenchmarkOn = false;
    }

    /**
     * turn on benchmark
     */
    public static function turnOnBenchmark()
    {
        self::$sessionBenchmarkOn = true;
    }
}
