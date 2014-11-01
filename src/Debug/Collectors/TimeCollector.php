<?php
/**
 * extends time data collector functionality
 *
 * @package     ClassBenchmark
 * @subpackage  Debug
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree
 * @link https://github.com/chajr/class-benchmark/wiki/
 */

namespace ClassBenchmark\Debug\Collectors;

use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DebugBarException;

class TimeCollector extends TimeDataCollector
{
    /**
     * Starts a measure
     *
     * @param string $name Internal name, used to stop the measure
     * @param string $label Public name
     */
    public function startMeasure($name, $label = null)
    {
        $start = microtime(true);
        $this->startedMeasures[$name] = array(
            'label' => $label ?: $name,
            'start' => $start,
            'memory'    => memory_get_usage()
        );
    }

    /**
     * Stops a measure
     *
     * @param string $name
     * @throws DebugBarException
     */
    public function stopMeasure($name)
    {
        $end = microtime(true);
        if (!$this->hasStartedMeasure($name)) {
            throw new DebugBarException("Failed stopping measure '$name' because it hasn't been started");
        }

        $this->addMeasure(
            $this->startedMeasures[$name]['label'],
            $this->startedMeasures[$name]['start'],
            $end,
            $this->startedMeasures[$name]['memory']
        );
        unset($this->startedMeasures[$name]);
    }

    /**
     * Adds a measure
     *
     * @param string $label
     * @param float $start
     * @param float $end
     * @param float|null $memory
     */
    public function addMeasure($label, $start, $end, $memory = null)
    {
        if ($memory !== null) {
            $memory = memory_get_usage() - $memory;
        }

        $duration = $this->getDataFormatter()->formatDuration($end - $start)
            . ' - '
            . $this->getDataFormatter()->formatBytes($memory);

        $this->measures[] = array(
            'label'             => $label,
            'start'             => $start,
            'relative_start'    => $start - $this->requestStartTime,
            'end'               => $end,
            'relative_end'      => $end - $this->requestEndTime,
            'duration'          => $end - $start,
            'duration_str'      => $duration,
        );
    }
}
