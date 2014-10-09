ClassBenchmark
============
Allow to check script performance and give some tools to debug script.


### Included libraries


Documentation
--------------
* [ClassBenchmark\Performance\Benchmark](https://github.com/chajr/class-benchmark/wiki/ClassBenchmark%5CPerformance%5CBenchmark)
* [ClassBenchmark\Debug\Tracer](https://github.com/chajr/class-benchmark/wiki/ClassBenchmark%5CDebug%5CTracer)
* [ClassBenchmark\Debug\Debugger]()


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
* ClassKernel library [class-kernel](https://github.com/chajr/class-kernel)

Change log
--------------
All release version changes:  
[Change log](https://githib.com/chajr/class-benchmark/CHANGELOG.md "Change log")

License
--------------
This bundle is released under the Apache 2.0 license.  
[Apache license](https://githib.com/chajr/class-benchmark/LICENSE "Apache license")