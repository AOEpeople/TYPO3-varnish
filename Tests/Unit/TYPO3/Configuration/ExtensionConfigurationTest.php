<?php
namespace Aoe\Varnish\TYPO3\Configuration;

/**
 * @covers Aoe\Varnish\TYPO3\Configuration\ExtensionConfiguration
 */
class ExtensionConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var boolean
     */
    protected $backupGlobals = true;

    /**
     * @test
     */
    public function isDebugShouldReturnTrue()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['varnish'] = serialize(array(
            'debug' => 1
        ));
        $configuration = new ExtensionConfiguration();
        $this->assertTrue($configuration->isDebug());
    }

    /**
     * @test
     */
    public function isDebugShouldReturnFalse()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['varnish'] = serialize(array(
            'debug' => 0
        ));
        $configuration = new ExtensionConfiguration();
        $this->assertFalse($configuration->isDebug());
    }

    /**
     * @test
     */
    public function getHostsShouldSingleHost()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['varnish'] = serialize(array(
            'hosts' => 'www.aoe.com'
        ));
        $configuration = new ExtensionConfiguration();
        $this->assertEquals(array('http://www.aoe.com'), $configuration->getHosts());
    }

    /**
     * @test
     */
    public function getHostsShouldMultipleHost()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['varnish'] = serialize(array(
            'hosts' => 'www.aoe.com,test.aoe.com,test1.aoe.com'
        ));
        $configuration = new ExtensionConfiguration();
        $this->assertEquals(array('http://www.aoe.com', 'http://test.aoe.com', 'http://test1.aoe.com',), $configuration->getHosts());
    }
}
