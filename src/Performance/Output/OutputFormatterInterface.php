<?php

namespace Benchmark\Performance\Output;

interface OutputFormatterInterface
{
    /**
     * @param array $output
     * @return string
     */
    public function formatOutput(array $output): string;
}
