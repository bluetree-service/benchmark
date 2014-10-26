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
     * @var StandardDebugBar
     */
    protected $_debugBar;

    /**
     * @var \DebugBar\JavascriptRenderer
     */
    protected $_debugBarRenderer;

    /**
     * allow to dynamic replace debugbar assets file directory
     * 
     * @var string
     */
    public static $assetsUrl = '';

    /**
     * create debugbar instance
     * 
     * @param string|null $assetsUrl
     */
    public function __construct($assetsUrl = null)
    {
        if (isset($assetsUrl)) {
            self::$assetsUrl = $assetsUrl;
        }

        $this->_debugBar            = Register::getSingleton('DebugBar\StandardDebugBar');
        $this->_debugBarRenderer    = $this->_debugBar
            ->getJavascriptRenderer()
            ->setBaseUrl(self::$assetsUrl)
            ->setEnableJqueryNoConflict(false);
    }

    protected function _addExtendedAssets()
    {
        $cssFiles = '';
        $this->_debugBarRenderer->addAssets($cssFiles, null);
    }

    /**
     * render debugbar header assets
     * 
     * @return string
     */
    public function renderHead()
    {
        return $this->_debugBarRenderer->renderHead();
    }

    /**
     * render debugbar
     * 
     * @return string
     */
    public function render()
    {
        return $this->_debugBarRenderer->render();
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
            $this->_debugBar->addCollector(
                Register::getObject($name)
            );
        }

        return $this;
    }

    /**
     * allow to quick access to data collector using method
     * 
     * @param string $collectorId
     * @param mixed $args
     * @return \DebugBar\DataCollector\DataCollectorInterface|null
     */
    public function __call($collectorId, $args)
    {
        if (isset($this->_debugBar[$collectorId])) {
            return $this->_debugBar[$collectorId];
        }

        return null;
    }
}
