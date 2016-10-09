BlueTree Service Benchmark
============

## Documentation

### Timer basic usage

1. Start benchmark by execute `\Benchmark\Performance\Timer::start()`, that will launch time storage.
2. To measure time from _start_ time, until some position use `::setMarker('some description')`, that will save time
execution and memory usage from start, or previous marker to current position.
3. After we set sime markers, use `::stop()` to halt benchmark.
4. Use `::calculateStats()` to get information about execution time and memory usage. It returns complete html, so to
show it just use `echo`.

```php
\Benchmark\Performance\Timer::start();

sleep 3;

\Benchmark\Performance\Timer::setMarker('sleep');

sleep 5;

\Benchmark\Performance\Timer::setMarker('another sleep');

\Benchmark\Performance\Timer::stop();
echo \Benchmark\Performance\Timer::calculateStats();

```

### Timer advanced usage

For more cleared view, we can join stats into groups, to see, how mutch time and memory use whole group. To begin measure
in group use `::startGroup('group name')`, set markers inside and use `::endGroup('group name')` with the same name to
store stats inside one group. Of course there is possibility to create nested groups.

### Timer other functions

* **start()** can have additional boolean argument, if false, then will whole timer will be disabled
* **turnOffBenchmark()** allow to turn off Timer
* **turnOnBenchmark()** allow to turn on Timer

### Screenshots

![]()

Install via Composer
--------------
To use packages you can just download package and pace it in your code. But recommended
way to use _ClassBenchmark_ is install it via Composer. To include _ClassBenchmark_
libraries paste into composer json:

```json
{
    "require": {
        "chajr/class-benchmark": "version"
    }
}
```

Required for ClassBenchmark libraries (ClassKernel) will be loaded automatically.

Project description
--------------

### Used conventions

* **Namespaces** - each library use namespaces
* **PSR-4** - [PSR-4](http://www.php-fig.org/psr/psr-4/) coding standard
* **Composer** - [Composer](https://getcomposer.org/) usage to load/update libraries

### Requirements

* PHP 5.4 or higher
* DOM extension enabled

Change log
--------------
All release version changes:  
[Change log](https://githib.com/chajr/class-benchmark/CHANGELOG.md "Change log")

License
--------------
This bundle is released under the Apache 2.0 license.  
[Apache license](https://githib.com/chajr/class-benchmark/LICENSE "Apache license")