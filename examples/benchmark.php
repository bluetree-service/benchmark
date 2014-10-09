<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../src/Performance/Benchmark.php';

use ClassKernel\Base\Register;
use ClassBenchmark\Performance\Benchmark;
?>
    <div class="example">
        <h3>Benchmark examples</h3>
        <div>
            <h5>Benchmark initialize</h5>
            <code>
                <pre>Benchmark::start()</pre>
            </code>
            <?php Benchmark::start() ?>

            <h5>Simple check script performance</h5>
            <code>
                <pre>for ($i = 0;$i < 10000;$i++) {preg_match('#[\d]#', $i);}
Benchmark::setMarker('test loop first iteration');
for ($i = 0;$i < 20000;$i++) {preg_match('#[\d]#', $i);}
Benchmark::setMarker('test loop second iteration');
Benchmark::stop();
echo Benchmark::display();</pre>
            </code>
            <?php for ($i = 0;$i < 10000;$i++) {preg_match('#[\d]#', $i);} ?>
            <?php Benchmark::setMarker('test loop first iteration'); ?>
            <?php for ($i = 0;$i < 20000;$i++) {preg_match('#[\d]#', $i);} ?>
            <?php Benchmark::setMarker('test loop second iteration'); ?>
            <?php Benchmark::stop(); ?>
            <?php echo Benchmark::display(); ?>

            <h5>Check performance in groups</h5>
            <code>
                <pre></pre>
            </code>

            <h5>With Register usage</h5>
            <code>
                <pre></pre>
            </code>
        </div>
    </div>
<?php
require_once __DIR__ . '/footer.php';