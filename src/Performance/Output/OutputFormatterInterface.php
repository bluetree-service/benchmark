<?php

namespace Benchmark\Performance\Output;

interface OutputFormatterInterface
{
    public function formatOutput(array $output);
}
