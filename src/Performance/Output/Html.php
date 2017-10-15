<?php

namespace Benchmark\Performance\Output;

class Html
{
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
}
