<?php

require_once 'vendor/autoload.php';

use Benchmark\Performance\Timer;

Timer::start();

for ($i = 0; $i < 5; $i++) {
    Timer::setMarker("val: $i");
}

Timer::startGroup('example group');

for ($i = 0; $i < 5; $i++) {
    Timer::setMarker("val: $i");
}

Timer::endGroup('example group');

Timer::stop();

//dump(Timer::calculateStats());
//dump(Timer::getFormattedOutput('raw+'));
//dump(Timer::getFormattedOutput('html'));
echo Timer::getFormattedOutput('html');
