<?php
require_once __DIR__ . '/../vendor/autoload.php';

use ClassKernel\Base\Register;
use ClassBenchmark\Debug\DebugBar;

Register::getObject(
    'ClassBenchmark\Debug\DebugBar',
    ['../vendor/maximebf/debugbar/src/DebugBar/Resources']
);
$debugBarHead   = DebugBar::renderHead();

require_once __DIR__ . '/header.php';


?>
    <div class="example">
        <h3>Debugbar</h3>
        <div>
           
        </div>
    </div>
<?php
DebugBar::dump(['asdasd', 'asdasd'], $label = 'bla', false);


DebugBar::startMeasure('test', 'Test');
for ($i = 0;$i < 10000;$i++) {preg_match('#[\d]#', $i);}
for ($ii = 0;$ii < 10000;$ii++) {preg_match('#[\d]#', $ii);}
DebugBar::stopMeasure('test');
DebugBar::startMeasure('test2', 'Test 2');
for ($iii = 0;$iii < 10000;$iii++) {preg_match('#[\d]#', $iii);}
DebugBar::stopMeasure('test2');

DebugBar::addInfo('test message');
DebugBar::addWarning(['test message', 'adadasd']);
echo DebugBar::render();
require_once __DIR__ . '/footer.php';