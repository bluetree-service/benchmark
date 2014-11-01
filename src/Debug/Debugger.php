<?php
/**
 * allow to lunch some basic collector classes
 *
 * @package     ClassBenchmark
 * @subpackage  Debug
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree
 * @link https://github.com/chajr/class-benchmark/wiki/
 */

namespace ClassBenchmark\Debug;

use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\MessagesCollector;
use ClassBenchmark\Debug\Collectors\TimeCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DebugBar as Debug;

class Debugger extends Debug
{
    /**
     * add basic required data collectors
     * 
     * @throws \DebugBar\DebugBarException
     */
    public function __construct()
    {
        $this->addCollector(new PhpInfoCollector());
        $this->addCollector(new MessagesCollector());
        $this->addCollector(new RequestDataCollector());
        $this->addCollector(new TimeCollector());
        $this->addCollector(new MemoryCollector());
        $this->addCollector(new ExceptionsCollector());
    }
}
