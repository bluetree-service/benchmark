<?php

declare(strict_types=1);

namespace Benchmark\Performance\Output;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class Html implements OutputFormatterInterface
{
    /**
     * @var \Twig\TemplateWrapper
     */
    protected $template;

    /**
     * Html constructor.
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../template');
        $env = new Environment($loader);
        $this->template = $env->load('html-output.html');
    }

    /**
     * prepare view and display list of markers, their times and percentage values
     *
     * @param array $output
     * @return string
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
