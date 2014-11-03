<?php
/**
 * extends debugbar functionality
 *
 * @package     ClassBenchmark
 * @subpackage  Debug
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree
 * @link https://github.com/chajr/class-benchmark/wiki/
 */

namespace ClassBenchmark\Debug;

use ClassKernel\Base\Register;
use DebugBar\DataCollector\DataCollectorInterface;
use Zend\Debug\Debug;
use Exception;

class DebugBar
{
    /**
     * collector codes
     */
    const MESSAGES              = 'messages';
    const TIME                  = 'time';
    const EXCEPTION             = 'exception';
    const MAIN_ASSETS           = 'main_assets';
    const ADDITIONAL_ASSETS     = 'additional_assets';

    /**
     * @var Debugger
     */
    protected static $_debugBar;

    /**
     * @var \DebugBar\JavascriptRenderer
     */
    protected static $_debugBarRenderer;

    /**
     * list of path to additional assets
     * asset_code_name => asset/path
     * 
     * @var string
     */
    protected static $_additionalAssetsPath = [];

    /**
     * create debugbar instance
     * 
     * @param array $options
     */
    public function __construct(array $options)
    {
        self::$_debugBar            = Register::getSingleton('ClassBenchmark\Debug\Debugger');
        self::$_debugBarRenderer    = self::$_debugBar
            ->getJavascriptRenderer()
            ->setBaseUrl($options[self::MAIN_ASSETS])
            ->setEnableJqueryNoConflict(false);

        if (isset($options[self::ADDITIONAL_ASSETS])) {
            self::$_additionalAssetsPath = $options[self::ADDITIONAL_ASSETS];
        }
    }

    /**
     * return path for additional assets
     * 
     * @param string|null $name
     * @return string
     */
    public static function getAdditionalPath($name = null)
    {
        if ($name && isset(self::$_additionalAssetsPath[$name])) {
            return self::$_additionalAssetsPath[$name];
        }

        return self::$_additionalAssetsPath;
    }

    /**
     * set asset path for given data collector key
     * 
     * @param string $name
     * @param string $path
     */
    public static function setAdditionalAssets($name, $path)
    {
        self::$_additionalAssetsPath[$name] = $path;
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
     * name can be an instance of lunched data collector object
     * 
     * @param string|DataCollectorInterface $name
     * @param bool $fullName
     * @return $this
     * @throws \DebugBar\DebugBarException
     */
    public function setCollector($name, $fullName = false)
    {
        if ($name instanceof DataCollectorInterface) {
            self::$_debugBar->addCollector($name);
            return $this;
        }

        $name = ucfirst($name);
        if (!$fullName) {
            $name = 'ClassBenchmark\Debug\Collectors\\' . $name;
        }

        if (class_exists($name)) {
            self::$_debugBar->addCollector(Register::getObject($name));
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

    /**
     * handle Zend\Debug::dump() function
     * 
     * @param mixed $data
     * @param string|null $label
     * @param bool $display
     * @return string|null
     */
    public static function dump($data, $label = null, $display = true)
    {
        $dump = Debug::dump($data, $label, $display);

        if (!$display) {
            $dump = preg_replace('#(^<pre>)|(</pre>$)#m', '', $dump);
            self::addInfo(html_entity_decode($dump));
            return null;
        }

        return $dump;
    }

    /**
     * Adds an exception to be profiled in the debug bar
     * 
     * @param Exception $exception
     */
    public function addException(Exception $exception)
    {
        /** @var \DebugBar\DataCollector\ExceptionsCollector $exceptionCollector */
        $exceptionCollector = self::$_debugBar[self::EXCEPTION];
        $exceptionCollector->addException($exception);
    }
}
