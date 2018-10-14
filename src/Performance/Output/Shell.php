<?php

namespace Benchmark\Performance\Output;

use Symfony\Component\Console\Output\ConsoleOutput;

class Shell  implements OutputFormatterInterface
{
    /**
     * @var ConsoleOutput
     */
    protected $output;

    public function __construct()
    {
        $this->output = new ConsoleOutput();
    }

    /**
     * prepare view and display list of markers, their times and percentage values
     */
    public function formatOutput(array $output) : string
    {
        return '';
    }
}
