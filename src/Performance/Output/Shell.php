<?php

namespace Benchmark\Performance\Output;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArgvInput;

class Shell  implements OutputFormatterInterface
{
    /**
     * @var ConsoleOutput
     */
    protected $output;
    
    /**
     * @var SymfonyStyle
     */
    protected $style;

    public function __construct()
    {
        $this->output = new ConsoleOutput();
        $this->style = new SymfonyStyle(new ArgvInput, $this->output);
    }

    /**
     * prepare view and display list of markers, their times and percentage values
     *
     * @param array $output
     * @return string
     */
    public function formatOutput(array $output) : string
    {
        $indent = '';
        $totalRuneTime = $output['total_rune_time'];
        $totalMemory = $output['total_memory'];
        $this->style->title(
            "Total application runtime: <info>$totalRuneTime</info>    Total memory usage: <info>$totalMemory</info>"
        );

        foreach ($output['markers'] as $marker) {
            if (preg_match('#.*group START$#', $marker['name'])) {
                $indent .= '    ';
                $this->style->writeln($indent . $marker['name']);
            } elseif (preg_match('#.*group END$#', $marker['name'])) {
                $this->style->writeln($indent . $marker['name']);
                $indent = substr($indent, 4, \strlen($indent));
            } else {
                $this->style->write(
                    "$indent{$marker['name']} <info>{$marker['time']}</info>"
                );
                $this->style->write(
                    "    <comment>{$marker['percentage']}</comment>"
                );
                $this->style->write(
                    "    <info>{$marker['memory']}</info>"
                );

                $this->style->newLine();
            }
        }

        $this->style->newLine();
        return PHP_EOL;
    }
}
