<?php
namespace Aoe\Varnish\TYPO3\Configuration;

use TYPO3\CMS\Core\SingletonInterface;

class ExtensionConfiguration implements SingletonInterface
{
    /**
     * @var array
     */
    private $configuration;

    public function __construct()
    {
        $this->configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['varnish']);
    }

    /**
     * @return boolean
     */
    public function isDebug()
    {
        return (boolean)$this->get('debug');
    }

    /**
     * @return array
     */
    public function getHosts()
    {
        $hosts = explode(',', $this->get('hosts'));
        array_walk($hosts, function (&$value) {
            if (false === strpos($value, 'https://') && false === strpos($value, 'http://')) {
                $value = 'http://' . $value;
            }
        });
        return $hosts;
    }

    /**
     * @param string $key
     * @return string
     */
    private function get($key)
    {
        return $this->configuration[$key];
    }
}
