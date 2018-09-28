<?php

namespace Benchmark\Debug;

/**
 * trace witch classes, files, functions are run
 *
 * @category    BlueTree Service Debug
 * @package     Benchmark
 * @subpackage  Debug
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree-service
 */
class Tracer
{
    /**
     * contains data to display
     * @var string
     */
    protected static $tmpDisplay = '';

    /**
     * keep information about marker times
     * @var array
     */
    protected static $session = [];

    /**
     * information that tracer is on, or off
     * @var boolean
     */
    protected static $tracerOn = true;

    /**
     * store styles for display
     * @var array
     */
    protected static $divStyles = [
        'c' => 'width:3%;float:left;padding:5px 0',
        'name' => 'width:12%;float:left;padding:5px 0',
        'time' => 'width:10%;float:left;padding:5px 0',
        'file' => 'width:16%;float:left;padding:5px 0',
        'line' => 'width:3%;float:left;padding:5px 0',
        'function' => 'width:11%;float:left;padding:5px 0',
        'class' => 'width:15%;float:left;padding:5px 0',
        'type' => 'width:0%;float:left;padding:5px 0',
        'args' => 'width:30%;float:left;padding:5px 0',
    ];

    /**
     * if set to false tracing data wont be displayed, for only saving file
     * @var boolean
     */
    public static $display = true;

    /**
     * contains number of step for current given marker
     * @var integer
     */
    public static $traceStep = 0;

    /**
     * starting tracing
     *
     * @param boolean $enabled
     */
    public static function start(bool $enabled = true) : void
    {
        if ($enabled) {
            self::marker(array('Tracer started'));
        } else {
            self::$tracerOn = false;
        }
    }

    /**
     * create marker with given data
     * optionally add debug_backtrace and marker background color
     * @param array $data
     *
     * @example marker(array('marker name'))
     * @example marker(array('marker name', debug_backtrace()))
     * @example marker(array('marker name', debug_backtrace(), '#000000'))
     */
    public static function marker(array $data) : void
    {
        $defaultData = ['', null, null];
        $data = array_merge($data, $defaultData);

        if ((bool)self::$tracerOn) {
            ++self::$traceStep;

            $time = microtime(true);
            $time = preg_split('#\[.,]#', $time);

            if (!isset($time[1])) {
                $time[1] = 0;
            }

            $markerTime = gmstrftime('%d-%m-%Y<br/>%H:%M:%S:', $time[0]) . $time[1];

            if (!$data[1]) {
                $data[1] = [
                    [
                        'file' => '',
                        'line' => '',
                        'function' => '',
                        'class' => '',
                        'type' => '',
                        'args' => ''
                    ]
                ];
            }

            if (isset($data[1][0]['args']) && \is_array($data[1][0]['args'])) {
                foreach ($data[1][0]['args'] as $arg => $val) {
                    if (\is_object($val)) {
                        $data[1][0]['args'][$arg] = serialize($val);
                    }
                }
            }

            self::$session['markers'][] = array(
                'time' => $markerTime,
                'name' => $data[0],
                'debug' => $data[1],
                'color' => $data[2]
            );
        }
    }

    /**
     * add information about stop tracing
     */
    public static function stop() : void
    {
        self::marker(array('Tracer ended'));
    }

    /**
     * return full tracing data as html content
     *
     * @return string
     */
    public static function display() : string
    {
        if (self::$tracerOn && self::$display) {
            self::stop();
            self::$display = '<div style="
            color: #FFFFFF;
            background-color: #3d3d3d;
            margin: 25px auto;
            width: 99%;
            text-align: center;
            padding:1%;
            overflow:hidden;
            font-size:12px;
            ">';

            self::$display .= '
                Tracer
                <br /><br />
            ';

            self::$display .= 'Markers time:<br /><div style="color:#fff;text-align:left">' . "\n";

            self::$display .= '
                    <div style="background-color:#6D6D6D">
                        <div style="' . self::$divStyles['c'] . '">C</div>
                        <div style="' . self::$divStyles['name'] . '">Name</div>
                        <div style="' . self::$divStyles['time'] . '">Time</div>
                        <div style="' . self::$divStyles['file'] . '">File</div>
                        <div style="' . self::$divStyles['line'] . '">Line</div>
                        <div style="' . self::$divStyles['function'] . '">Function</div>
                        <div style="' . self::$divStyles['class'] . '">Class</div>
                        <!--<div style="' . self::$divStyles['type'] . '">T</div>-->
                        <div style="' . self::$divStyles['args'] . '">Arguments</div>
                        <div style="clear:both"></div>
                    </div>
                ';

            $counter = 0;

            foreach (self::$session['markers'] as $marker) {
                if ($counter %2) {
                    $background = 'background-color:#4D4D4D';
                } else {
                    $background = '';
                }

                if ($marker['color']) {
                    $background = 'background-color:' . $marker['color'];
                }

                self::$display .= '<div style="' . $background . '">
                    <div style="' . self::$divStyles['c'] . '">'
                    . ++$counter . '</div>'."\n";

                self::$display .= '<div style="' . self::$divStyles['name'] . '">'
                    . $marker['name'] . '</div>'."\n";

                self::$display .= '<div style="' . self::$divStyles['time'] . '">'
                    . $marker['time'] . '</div>'."\n";

                self::$display .= '<div style="' . self::$divStyles['file'] . '">'
                    . $marker['debug'][0]['file'] . '</div>'."\n";

                self::$display .= '<div style="' . self::$divStyles['line'] . '">'
                    . $marker['debug'][0]['line'] . '</div>'."\n";

                self::$display .= '<div style="' . self::$divStyles['function'] . '">'
                    . $marker['debug'][0]['function'] . '</div>'."\n";

                self::$display .= '<div style="' . self::$divStyles['class'] . '">'
                    . $marker['debug'][0]['class'] . '</div>'."\n";

                self::$display .= '<div style="' . self::$divStyles['args'] . '"><pre>'
                    . var_export($marker['debug'][0]['args'], true)
                    . ' </pre></div>'."\n";

                self::$display .= '<div style="clear:both"></div></div>';
            }
            self::$display .= '</div></div>';
        }

        return self::$display;
    }

    /**
     * save tracing data to log file
     *
     * @param string $filePath
     */
    public static function saveToFile(string $filePath) : void
    {
        if (self::$tracerOn) {
            self::display();
            self::$display .= '<pre>' . var_export($_SERVER, true) . '</pre>';

            file_put_contents($filePath, self::$display);
        }
    }

    /**
     * turn off tracer
     */
    public static function turnOffTracer() : void
    {
        self::$tracerOn = false;
    }

    /**
     * turn on tracer
     */
    public static function turnOnTracer() : void
    {
        self::$tracerOn = true;
    }
}
