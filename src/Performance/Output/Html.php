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
    }
}
