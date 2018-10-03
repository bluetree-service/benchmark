<?php

namespace Benchmark\Performance\Output;

class Html
{
    /**
     * prepare view and display list of markers, their times and percentage values
     */
    public function formatOutput(array $output, callable $formatter) : string
    {

        $display = '<div style="
        color: #FFFFFF;
        background-color: #3d3d3d;
        border: 1px solid #FFFFFF;
        width: 90%;
        text-align: center;
        margin: 25px auto;
        ">';

        $display .= '
            Total application runtime: ' . $output['total_rune_time'] . '&nbsp;&nbsp;&nbsp;&nbsp;
            Total memory usage: ' . $output['total_memory'] . '<br /><br />';
        $display .= 'Marker times:<br /><table style="width:100%">'."\n";

        if (isset($outputp['markers'])) {
            foreach ($output['markers'] as $marker) {
                if ($marker['marker_color']) {
                    $additionalColor = 'background-color:#' . dechex($marker['marker_color']);
                } else {
                    $additionalColor = '';
                }

                if ($marker['marker_time'] === '') {
                    $time = '-';
                    $percent = '-';
                    $ram = '-';
                } else {
                    $ram = ($marker['marker_memory'] - self::$sessionMemoryStart) / 1024;
                    $ram = number_format($ram, 3, ',', '');
                    $percent = ($marker['marker_time'] / $total) *100000;
                    $percent = number_format($percent, 5);
                    $time = number_format(
                        $marker['marker_time'] *1000,
                        5,
                        '.',
                        ' '
                    );
                    $time .= ' ms';
                    $percent .= ' %';
                    $ram .= ' kB';
                }

                $display .= '<tr style="' . $additionalColor . '">
                <td style="width:40%;color:#fff">' . $marker['marker_name'] . '</td>' . "\n";
                $display .= '<td style="width:20%;color: #fff;">' . $time . '</td>'."\n";
                $display .= '<td style="width:20%;color: #fff;">' . $percent . '</td>'."\n";
                $display .= '<td style="width:20%;color:#fff">' . $ram . '</td>
                </tr>' . "\n";
            }
        }

        $display .= '</table></div>';

        return $display;
    }
}
