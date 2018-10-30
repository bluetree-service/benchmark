<?php

namespace Tests;

use Benchmark\Performance\Timer;

class TimerTest extends \PHPUnit\Framework\TestCase
{
    public function testBasic()
    {
        Timer::start();
        Timer::setMarker('First check.');

        sleep(2);

        Timer::setMarker('Sleep 2');
        Timer::stop();

        $stats = Timer::calculateStats();

        $this->assertArrayHasKey('total_rune_time', $stats);
        $this->assertArrayHasKey('total_memory', $stats);
        $this->assertArrayHasKey('markers', $stats);

        $this->assertArrayHasKey('color', $stats['markers'][0]);
        $this->assertArrayHasKey('name', $stats['markers'][0]);
        $this->assertArrayHasKey('time', $stats['markers'][0]);
        $this->assertArrayHasKey('percentage', $stats['markers'][0]);
        $this->assertArrayHasKey('memory', $stats['markers'][0]);

        $this->assertArrayHasKey('color', $stats['markers'][1]);
        $this->assertArrayHasKey('name', $stats['markers'][1]);
        $this->assertArrayHasKey('time', $stats['markers'][1]);
        $this->assertArrayHasKey('percentage', $stats['markers'][1]);
        $this->assertArrayHasKey('memory', $stats['markers'][1]);

        $this->assertEquals('First check.', $stats['markers'][0]['name']);
        $this->assertEquals('Sleep 2', $stats['markers'][1]['name']);
    }
}
