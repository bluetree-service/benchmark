<?php
/**
 * extends debugbar functionality
 *
 * @package     ClassBenchmark
 * @subpackage  Performance
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree
 * @link https://github.com/chajr/class-benchmark/wiki/ClassBenchmark%5CPerformance%5CBenchmark
 */

namespace ClassBenchmark\Debug;

use DebugBar\StandardDebugBar;
use ClassKernel\Base\Register;

class DebugBar
{
    /**
     * collector codes
     */
    const MESSAGES  = 'messages';
    const TIME      = 'time';

    /**
     * @var StandardDebugBar
     */
    protected static $_debugBar;

    /**
     * @var \DebugBar\JavascriptRenderer
     */
    protected static $_debugBarRenderer;

    /**
     * create debugbar instance
     * 
     * @param array $options
     */
    public function __construct(array $options)
    {
        self::$_debugBar            = Register::getSingleton('DebugBar\StandardDebugBar');
        self::$_debugBarRenderer    = self::$_debugBar
            ->getJavascriptRenderer()
            ->setBaseUrl($options[0])
            ->setEnableJqueryNoConflict(false);
    }

    /**
     * allow to set extended assets
     * 
     * @return $this
     */
    protected function _addExtendedAssets()
    {
        $cssFiles = '';
        self::$_debugBarRenderer->addAssets($cssFiles, null);

        return $this;
    }

    /**
     * render debugbar header assets
     * 
     * @return string
     */
    public static function renderHead()
    {
        return self::$_debugBarRenderer->renderHead();
    }

    /**
     * render debugbar
     * 
     * @return string
     */
    public static function render()
    {
        return self::$_debugBarRenderer->render();
    }

    /**
     * set collector by given name
     * 
     * @param $name
     * @param bool $fullName
     * @return $this
     * @throws \DebugBar\DebugBarException
     */
    public function setCollector($name, $fullName = false)
    {
        $name = ucfirst($name);
        if (!$fullName) {
            $name = 'ClassBenchmark\Debug\Collectors\\' . $name;
        }

        if (class_exists($name)) {
            self::$_debugBar->addCollector(
                Register::getObject($name)
            );
        }

        return $this;
    }

    /**
     * allow to quick access to data collector using static method
     * 
     * @param string $collectorId
     * @param mixed $args
     * @return \DebugBar\DataCollector\DataCollectorInterface|null
     */
    public static function __callStatic($collectorId, $args)
    {
        if (isset(self::$_debugBar[$collectorId])) {
            return self::$_debugBar[$collectorId];
        }

        return null;
    }

    /**
     * set info message
     * 
     * @param mixed $message
     * @param bool $isString
     */
    public static function addInfo($message, $isString = false)
    {
        self::_addMessage($message, 'info', $isString);
    }

    /**
     * set warning message
     * 
     * @param mixed $message
     * @param bool $isString
     */
    public static function addWarning($message, $isString = false)
    {
        self::_addMessage($message, 'warning', $isString);
    }

    /**
     * set error message
     * 
     * @param mixed $message
     * @param bool $isString
     */
    public static function addError($message, $isString = false)
    {
        self::_addMessage($message, 'error', $isString);
    }

    /**
     * set success message
     * 
     * @param mixed $message
     * @param bool $isString
     */
    public static function addSuccess($message, $isString = false)
    {
        self::_addMessage($message, 'success', $isString);
    }

    /**
     * common method to add message
     * 
     * @param mixed $message
     * @param string $label
     * @param bool $isString
     */
    protected static function _addMessage($message, $label, $isString)
    {
        /** @var \DebugBar\DataCollector\MessagesCollector $messageCollector */
        $messageCollector = self::$_debugBar[self::MESSAGES];
        $messageCollector->addMessage($message, $label, $isString);
    }

    /**
     * Starts a measure
     * 
     * @param string $code
     * @param string $description
     */
    public static function startMeasure($code, $description)
    {
        self::_measure()->startMeasure($code, $description);
    }

    /**
     * Stops a measure
     * 
     * @param string $code
     * @throws \DebugBar\DebugBarException
     */
    public static function stopMeasure($code)
    {
        self::_measure()->stopMeasure($code);
    }

    /**
     * measure the execution of a Closure
     * 
     * @param string $message
     * @param \Closure $closure
     */
    public static function measure($message, \Closure $closure)
    {
        self::_measure()->measure($message, $closure);
    }

    /**
     * return TimeDataCollector instance
     * 
     * @return \DebugBar\DataCollector\TimeDataCollector
     */
    protected static function _measure()
    {
        return self::$_debugBar[self::TIME];
    }
}
