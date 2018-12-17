BlueTree Service Benchmark
============

## Documentation

### Timer basic usage

1. Start benchmark by execute `\Benchmark\Performance\Timer::start()`, that will launch time storage.
2. To measure time from _start_ time, until some position use `::setMarker('some description')`, that will save time
execution and memory usage from start, or previous marker to current position.
3. After we set sime markers, use `::stop()` to halt benchmark.
4. Use `::calculateStats()` to get information about execution time and memory usage.

```php
\Benchmark\Performance\Timer::start();

sleep 3;

\Benchmark\Performance\Timer::setMarker('sleep');

sleep 5;

\Benchmark\Performance\Timer::setMarker('another sleep');

\Benchmark\Performance\Timer::stop();
var_dump(\Benchmark\Performance\Timer::calculateStats());
```

5. If you want to get formatted output, look down for __Timer formatting output__ section.

### Timer advanced usage

For more cleared view, we can join stats into groups, to see, how mutch time and memory use whole group. To begin measure
in group use `::startGroup('group name')`, set markers inside and use `::endGroup('group name')` with the same name to
store stats inside one group. Of course there is possibility to create nested groups.

### Timer other functions

* **start()** can have additional boolean argument, if false, then will whole timer will be disabled
* **turnOffBenchmark()** allow to turn off Timer
* **turnOnBenchmark()** allow to turn on Timer

### Timer formatting output
There are three possibilities to get formatted output. First one allow to ger output as array with formatted time, memory
and percentage values. second one allow to get output formatted by _Symfony Console_ output class. And last one allow
to get output as _HTML_.

#### Pre-formatted array
After `\Benchmark\Performance\Timer::stop();` execute `(Timer::getFormattedOutput('raw+')` to get array with formatted
memory, time and percentage. Using __raw__ as parameter return raw values, the same as usage of
 `\Benchmark\Performance\Timer::calculateStats();`

#### Console output
After `\Benchmark\Performance\Timer::stop();` execute `(Timer::getFormattedOutput('shell')` to get formatted output for
console. Output will be equivalent of that:  
```
Total application runtime: ~1 001.8280 ms    Total memory usage: ~3602 kB
=========================================================================

val: 0 ~0.0050 ms    0.00050 %    ~0,000 kB
val: 1 ~100.1649 ms    9.99821 %    ~0,000 kB
val: 2 ~100.1842 ms    10.00014 %    ~0,000 kB
val: 3 ~100.1740 ms    9.99912 %    ~0,000 kB
val: 4 ~100.1639 ms    9.99812 %    ~0,000 kB
    example group START
    val: 0 ~100.2018 ms    10.00190 %    ~0,000 kB
    val: 1 ~100.1649 ms    9.99821 %    ~0,000 kB
    val: 2 ~100.1790 ms    9.99962 %    ~0,000 kB
    val: 3 ~100.1539 ms    9.99712 %    ~0,000 kB
    val: 4 ~100.1499 ms    9.99671 %    ~0,000 kB
    example group END
```

#### HTML output
After `\Benchmark\Performance\Timer::stop();` execute `(Timer::getFormattedOutput('html')` to get formatted output for
browser. Output will be equivalent of that:  
```html
<div style="
    color: #fff;
    background-color: #000;
    border: 1px solid #fff;
    width: 90%;
    text-align: center;
    margin: 25px auto;
">
    Total application runtime: ~1 001.8280 ms&nbsp;&nbsp;&nbsp;&nbsp;Total memory usage: ~3129.140625 kB
    <br />
    <br />
    Marker times:
    <br />
    <table style="width:100%">
        <tr style="background-color:#202020">
            <td style="width:40%;color:#fff">val: 0</td>
            <td style="width:20%;color: #fff;">~0.0050 ms</td>
            <td style="width:20%;color: #fff;">0.00050 %</td>
            <td style="width:20%;color:#fff">~0,000 kB</td>
        </tr>
```

### Write own output formatter
If you want to get data in you own specified format, you can apply it to method `calculateStats`. This method accept
`callable` type to process data array before it will be returned.  
This is how it looks inside the original code: `self::calculateStats([Formatter::class, 'formatValues']);` - this will
execute method `formatValues` from `Formatter::class` class.

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