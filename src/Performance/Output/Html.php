<?php

namespace Benchmark\Performance\Output;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class Html implements OutputFormatterInterface
{
    /**
     * @var \Twig\TemplateWrapper
     */
    protected $template;

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../template');
        $env = new Environment($loader);
        $this->template = $env->load('html-output.html');

//        echo $this->template->render(['total_time' => 'adsfdasfsdfdsf']);
//        exit;
    }

    /**
     * prepare view and display list of markers, their times and percentage values
     */
    public function formatOutput(array $output) : string
    {
        return $this->template->render([
            'total_time' => $output['total_rune_time'],
            'total_memory' => $output['total_memory'],
            'markers' => $output['markers']
        ]);
        
        
//        $display = '<div style="
//        color: #FFFFFF;
//        background-color: #3d3d3d;
//        border: 1px solid #FFFFFF;
//        width: 90%;
//        text-align: center;
//        margin: 25px auto;
//        ">';
//
//        $display .= '
//            Total application runtime: ' . $output['total_rune_time'] . '&nbsp;&nbsp;&nbsp;&nbsp;
//            Total memory usage: ' . $output['total_memory'] . '<br /><br />';
//        $display .= 'Marker times:<br /><table style="width:100%">'."\n";
//
//        if (isset($output['markers'])) {
//            foreach ($output['markers'] as $marker) {
//                $additionalColor = $marker['color'] ?: 'background-color:#' . dechex($marker['color']);
//                $display .= '<tr style="' . $additionalColor . '">
//                <td style="width:40%;color:#fff">' . $marker['name'] . '</td>' . "\n";
//                $display .= '<td style="width:20%;color: #fff;">' . $marker['time'] . '</td>'."\n";
//                $display .= '<td style="width:20%;color: #fff;">' .  $marker['percentage'] . '</td>'."\n";
//                $display .= '<td style="width:20%;color:#fff">' . $marker['memory'] . '</td>
//                </tr>' . "\n";
//            }
//        }
//
//        $display .= '</table></div>';
//
//        return $display;
    }
}
