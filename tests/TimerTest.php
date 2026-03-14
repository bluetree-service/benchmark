<?php

namespace Tests;

use Benchmark\Performance\Output\Formatter;
use Benchmark\Performance\Timer;

class TimerTest extends \PHPUnit\Framework\TestCase
{
    public function testBasic()
    {
        $currentTime = $this->basicTest(true);

        $this->assertIsFloat($currentTime);
        $this->assertGreaterThanOrEqual(2, $currentTime);
        $this->assertLessThan(3, $currentTime);

        $stats = Timer::calculateStats();
        
        $this->assertIsFloat($stats['total_rune_time']);
        $this->assertIsInt($stats['total_memory']);
        $this->assertIsFloat($stats['markers'][0]['time']);
        $this->assertIsFloat($stats['markers'][0]['percentage']);

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

    public function testFormattingValues()
    {
        $this->basicTest(true);

        $stats = Timer::calculateStats([Formatter::class, 'formatValues']);

        $this->assertMatchesRegularExpression('/~[\d] [\d]*\.[\d]* ms/', $stats['total_rune_time']);
        $this->assertMatchesRegularExpression('/~[\d] [\d]*\.[\d]* ms/', $stats['markers'][1]['time']);
        $this->assertMatchesRegularExpression('/~[\d]*\.[\d]* kB/', $stats['total_memory']);
        
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

    public function testWithGroups()
    {
        Timer::start();
        Timer::setMarker('First check.');
        Timer::startGroup('Group 1');
        
        Timer::setMarker('Group 1 - check 1');
        sleep(2);
        Timer::setMarker('Group 1 - Sleep 2');
        
        Timer::endGroup('Group 1');
        Timer::stop();

        $data = Timer::calculateStats();
        $this->assertEquals('Group 1 START', $data['markers'][5]['name']);
        $this->assertEquals('-', $data['markers'][5]['time']);
    }

    public function testDisabled()
    {
        $this->basicTest(false);

        $stats = Timer::calculateStats();
        $this->assertEmpty($stats);

        Timer::turnOnBenchmark();
        $this->basicTest(true);

        $stats = Timer::calculateStats();

        $this->assertIsFloat($stats['total_rune_time']);
        $this->assertIsInt($stats['total_memory']);
        $this->assertIsFloat($stats['markers'][0]['time']);
        $this->assertIsFloat($stats['markers'][0]['percentage']);

        Timer::turnOffBenchmark();

        $stats = Timer::calculateStats();
        $this->assertEmpty($stats);
    }

    public function testFormatOutputRaw()
    {
        $this->basicTest(true);

        $stats = Timer::getFormattedOutput('raw+');
        $this->assertMatchesRegularExpression('/~[\d] [\d]*\.[\d]* ms/', $stats['total_rune_time']);
        $this->assertMatchesRegularExpression('/~[\d] [\d]*\.[\d]* ms/', $stats['markers'][1]['time']);
        $this->assertMatchesRegularExpression('/~[\d]*\.[\d]* kB/', $stats['total_memory']);

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
    
    public function testFormatOutputShell()
    {
        $this->basicTest(true);

        $output = Timer::getFormattedOutput('shell', true);

        $this->assertStringContainsString('Total application runtime:', $output);
        $this->assertStringContainsString('Total memory usage:', $output);
        $this->assertStringContainsString('====================================', $output);
        $this->assertStringContainsString('Sleep 2', $output);
        $this->assertStringContainsString('First check', $output);
    }
    
    public function testFormatOutputHtml()
    {
        $this->basicTest(true);

        $output = Timer::getFormattedOutput('html', true);

        $this->assertStringContainsString('Total application runtime:', $output);
        $this->assertStringContainsString('Total memory usage:', $output);
        $this->assertStringContainsString('<table style="width:100%">', $output);
        $this->assertStringContainsString('<td style="width:40%;color:#fff">First check.</td>', $output);
        $this->assertStringContainsString('<tr style="background-color:#202020">', $output);
    }

    public function testSavetoFile()
    {
        $this->basicTest(true);

        $stats = Timer::calculateStats();

        touch(__DIR__ . '/test_output.txt');
        Timer::toFile($stats, __DIR__ . '/test_output.txt');
        
        $this->assertFileExists(__DIR__ . '/test_output.txt');
        $this->assertStringContainsString('total_rune_time', file_get_contents(__DIR__ . '/test_output.txt'));
        $this->assertStringContainsString('total_memory', file_get_contents(__DIR__ . '/test_output.txt'));
        $this->assertStringContainsString('markers', file_get_contents(__DIR__ . '/test_output.txt'));
        
        $this->expectException('DomainException');
        Timer::toFile($stats, __DIR__ . '/none_existing_file.txt');
    }

    protected function basicTest(bool $enabled)
    {
        Timer::start($enabled);
        Timer::setMarker('First check.');

        sleep(2);
        $currentTime = Timer::getCurrentTime();

        Timer::setMarker('Sleep 2');
        Timer::stop();
        
        return $currentTime;
    }
}
