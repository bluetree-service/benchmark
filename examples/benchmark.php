#!/usr/bin/php

<?php

require_once 'vendor/autoload.php';

use Benchmark\Performance\Timer;

Timer::start();

for ($i = 0; $i < 5; $i++) {
    Timer::setMarker("val: $i");
    usleep(100000);
}

Timer::startGroup('example group');

for ($i = 0; $i < 5; $i++) {
    Timer::setMarker("val: $i");
    usleep(100000);
}

Timer::endGroup('example group');

Timer::stop();

dump(Timer::calculateStats());
dump(Timer::getFormattedOutput('raw+'));
echo Timer::getFormattedOutput('html');
Timer::getFormattedOutput('shell');
