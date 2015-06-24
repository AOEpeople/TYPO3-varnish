<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 AOE GmbH <dev@aoe.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace AOE\Varnish\TYPO3\Configuration;

/**
 * @package AOE\Varnish
 * @covers AOE\Varnish\TYPO3\Configuration\ExtensionConfiguration
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
        $this->assertEquals(array('www.aoe.com'), $configuration->getHosts());
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
        $this->assertEquals(array('www.aoe.com', 'test.aoe.com', 'test1.aoe.com',), $configuration->getHosts());
    }
}
