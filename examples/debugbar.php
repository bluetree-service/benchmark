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
DebugBar::addInfo('test message');
echo DebugBar::render();
require_once __DIR__ . '/footer.php';