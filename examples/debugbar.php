<?php
require_once __DIR__ . '/../vendor/autoload.php';

use ClassKernel\Base\Register;
use ClassBenchmark\Debug\DebugBar;

DebugBar::$assetsUrl ='../vendor/maximebf/debugbar/src/DebugBar/Resources';
/** @var ClassBenchmark\Debug\DebugBar $debugBar */
$debugBar       = Register::getObject('ClassBenchmark\Debug\DebugBar');
$debugBarHead   = $debugBar->renderHead();

require_once __DIR__ . '/header.php';


?>
    <div class="example">
        <h3>Debugbar</h3>
        <div>
           
        </div>
    </div>
<?php
echo $debugBar->render();
require_once __DIR__ . '/footer.php';