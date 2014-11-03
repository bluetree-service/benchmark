<?php
/**
 * collect information about creating objects by ClassKernel\Base\Register
 *
 * @package     ClassBenchmark
 * @subpackage  Debug
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree
 * @link https://github.com/chajr/class-benchmark/wiki/
 */

namespace ClassBenchmark\Debug\Collectors;

use DebugBar\DebugBarException;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use ClassKernel\Base\Register;
use ReflectionClass;

class RegisterCollector extends DataCollector implements Renderable
{
    const NAME = 'register';

    public function getWidgets()
    {
        return [
            'register' => [
                "icon"      => "tasks",
                "tooltip"   => "Number of called objects",
                "map"       => "register.object_widget",
                "default"   => "0"
            ],
//            'register_data' => [
//                "icon"      => "tasks",
//                "widget"    => "PhpDebugBar.Widgets.VariableListWidget",
//                "map"       => "time",
//                "default"   => "[]"
//            ]
        ];
    }

    public function collect()
    {
        $objects            = 0;
        $registerReflection = new ReflectionClass(new Register);
        $singletons         = count(
            $registerReflection->getStaticProperties()['_singletons']
        );

        foreach (Register::getClassCounter() as $counter) {
            if (is_int($counter)) {
                $objects += $counter;
            }
        }

        return [
            'object_widget' => "$objects-O $singletons-S"
        ];
    }

    public function getAssets()
    {

    }

    /**
     * Returns the unique name of the collector
     * 
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}
