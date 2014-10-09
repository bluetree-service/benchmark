<?php
/**
 * allows to check script performance
 *
 * @package     ClassBenchmark
 * @subpackage  Performance
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree
 * @link https://github.com/chajr/class-benchmark/wiki/ClassBenchmark%5CPerformance%5CBenchmark
 */

namespace ClassBenchmark\Performance;

class Benchmark
{
    /**
     * information that marker time goes to group
     * 
     * @var array
     */
    protected static $_groupOn = [];

    /**
     * contains array of times for group of markers
     * 
     * @var array
     */
    protected static $_group = [];

    /**
     * contains data about benchmark started memory usage
     * 
     * @var integer
     */
    protected static $_sessionMemoryStart = 0;

    /**
     * contains data about started benchmark session time
     * 
     * @var integer
     */
    protected static $_sessionBenchmarkStart = 0;

    /**
     * contains data about benchmark marker time
     * 
     * @var integer
     */
    protected static $_sessionBenchmarkMarker = 0;

    /**
     * contains data about benchmark markers
     * 
     * @var array
     */
    protected static $_sessionBenchmarkMarkers = [];

    /**
     * contains data about benchmark finish time
     * 
     * @var integer
     */
    protected static $_sessionBenchmarkFinish = 0;

    /**
     * contains information that benchmark is on or off
     * 
     * @var bool
     */
    protected static $_sessionBenchmarkOn = true;

    /**
     * contains color information for group of markers
     * 
     * @var int
     */
    protected static $_backgroundColor = 0x3d3d3d;

    /**
     * start benchmark, and set in internal session start time
     *
     * @param boolean $on
     */
    public static function start($on = true)
    {
        if ($on) {
            $time                           = microtime(true);
            self::$_sessionMemoryStart      = memory_get_usage();
            self::$_sessionBenchmarkStart   = $time;
            self::$_sessionBenchmarkMarker  = $time;
        } else {
            self::$_sessionBenchmarkOn      = false;
        }
    }

    /**
     * set marker and set in session time of run up to current position
     *
     * @param string $name name of marker
     */
    public static function setMarker($name)
    {
        if (!self::$_sessionBenchmarkOn) {
            return;
        }

        $markerTime     = microtime(true) - self::$_sessionBenchmarkMarker;
        $markerColor    = false;

        if (!empty(self::$_groupOn)) {
            $markerColor = self::$_backgroundColor;
            self::_groupMarkers($markerTime);
        }

        self::$_sessionBenchmarkMarker    = microtime(true);
        self::$_sessionBenchmarkMarkers[] = array(
            'marker_name'       => $name,
            'marker_time'       => $markerTime,
            'marker_memory'     => memory_get_usage(),
            'marker_color'      => $markerColor
        );
    }

    /**
     * set data into marker group
     * 
     * @param int $markerTime
     */
    protected static function _groupMarkers($markerTime)
    {
        foreach (self::$_group as $marker => $info) {
            if (!isset(self::$_group[$marker]['time'])) {
                $groupMarkerTime = $markerTime;
            } else {
                $groupMarkerTime = self::$_group[$marker]['time'] + $markerTime;
            }
            self::$_group[$marker]['time'] = $groupMarkerTime;
        }
    }

    /**
     * start group of markers
     *
     * @param string $groupName
     */
    public static function startGroup($groupName)
    {
        if (!self::$_sessionBenchmarkOn) {
            return;
        }

        self::$_backgroundColor += 0x101010;

        self::$_sessionBenchmarkMarkers[] = [
            'marker_name'       => $groupName . ' START',
            'marker_time'       => '',
            'marker_memory'     => '',
            'marker_color'      => self::$_backgroundColor
        ];

        self::$_group[$groupName]['memory'] = memory_get_usage();
        self::$_groupOn[$groupName]         = $groupName;
    }

    /**
     * end counting given group of markers
     * 
     * @param string $groupName
     */
    public static function endGroup($groupName)
    {
        if (!self::$_sessionBenchmarkOn) {
            return;
        }

        unset(self::$_groupOn[$groupName]);
        $memoryUsage = memory_get_usage() - self::$_group[$groupName]['memory'];

        self::$_sessionBenchmarkMarkers[] = array(
            'marker_name'       => $groupName . ' END',
            'marker_time'       => self::$_group[$groupName]['time'],
            'marker_memory'     => $memoryUsage,
            'marker_color'      => self::$_backgroundColor
        );

        self::$_backgroundColor -= 0x101010;
    }

    /**
     * stop benchmark, and time counting, save last run time
     */
    public static function stop()
    {
        if (self::$_sessionBenchmarkOn) {
            self::$_sessionBenchmarkFinish = microtime(true);
        }
    }

    /**
     * prepare view and display list of markers, their times and percentage values
     * 
     * @return string
     */
    public static function display()
    {
        $display = '';

        if (!self::$_sessionBenchmarkOn) {
            return $display;
        }

        $display = '<div style="
        color: #FFFFFF;
        background-color: #3d3d3d;
        border: 1px solid #FFFFFF;
        width: 90%;
        text-align: center;
        margin: 25px auto;
        ">';

        $benchmarkStartTime = self::$_sessionBenchmarkStart;
        $benchmarkEndTime   = self::$_sessionBenchmarkFinish;
        $total              = ($benchmarkEndTime - $benchmarkStartTime) *1000;
        $formatTime         = number_format($total, 5, '.', ' ');
        $memoryUsage        = memory_get_usage()/1024;

        $display .= '
            Total application runtime: ' . $formatTime . ' ms&nbsp;&nbsp;&nbsp;&nbsp;
            Total memory usage: '. number_format($memoryUsage, 3, ',', '')
            . ' kB<br /><br />';

        $display .= 'Marker times:<br /><table style="width:100%">'."\n";

        foreach (self::$_sessionBenchmarkMarkers as $marker) {
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
                $ram      = ($marker['marker_memory'] - self::$_sessionMemoryStart) / 1024;
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
            $display .= '<td style="width:20%">' . $time . '</td>'."\n";
            $display .= '<td style="width:20%">' . $percent . '</td>'."\n";
            $display .= '<td style="width:20%;color:#fff">' . $ram . '</td>
                </tr>' . "\n";
        }
        $display .= '</table></div>';

        return $display;
    }

    /**
     * turn off benchmark
     */
    public static function turnOffBenchmark()
    {
        self::$_sessionBenchmarkOn = false;
    }

    /**
     * turn on benchmark
     */
    public static function turnOnBenchmark()
    {
        self::$_sessionBenchmarkOn = true;
    }
}
