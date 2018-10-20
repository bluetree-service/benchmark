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
        $totalRuneTime = $output['total_rune_time'];
        $totalMemory = $output['total_memory'];
        $this->output->writeln(<<<EOT
            Total application runtime: $totalRuneTime&nbsp;&nbsp;&nbsp;&nbsp;Total memory usage: $totalMemory
EOT
        );

        $this->output->writeln('');
        foreach ($output['markers'] as $marker) {
            $this->output->writeln($marker['name'] . $marker['time'] . $marker['percentage'] . $marker['memory']);
        }

        return PHP_EOL;
    }
}
