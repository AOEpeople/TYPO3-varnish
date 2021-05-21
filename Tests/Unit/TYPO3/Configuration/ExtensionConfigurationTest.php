<?php
namespace Aoe\Varnish\Tests\Unit\TYPO3\Configuration;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 AOE GmbH <dev@aoe.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

use Aoe\Varnish\TYPO3\Configuration\ExtensionConfiguration;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * @covers \Aoe\Varnish\TYPO3\Configuration\ExtensionConfiguration
 */
class ExtensionConfigurationTest extends UnitTestCase
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
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['varnish'] = ['debug' => 1];
        $configuration = new ExtensionConfiguration();
        $this->assertTrue($configuration->isDebug());
    }

    /**
     * @test
     */
    public function isDebugShouldReturnFalse()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['varnish'] = ['debug' => 0];
        $configuration = new ExtensionConfiguration();
        $this->assertFalse($configuration->isDebug());
    }

    /**
     * @test
     */
    public function getHostsShouldSingleHost()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['varnish'] = ['hosts' => 'www.aoe.com'];
        $configuration = new ExtensionConfiguration();
        $this->assertEquals(array('http://www.aoe.com'), $configuration->getHosts());
    }

    /**
     * @test
     */
    public function getHostsShouldMultipleHost()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['varnish'] =
            [
                'hosts' => 'www.aoe.com,test.aoe.com,test1.aoe.com'
            ];
        $configuration = new ExtensionConfiguration();
        $this->assertEquals(array('http://www.aoe.com', 'http://test.aoe.com', 'http://test1.aoe.com',), $configuration->getHosts());
    }

    /**
     * @test
     */
    public function getDefaultTimeoutShouldReturnInteger()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['varnish'] = ['default_timeout' => '0'];
        $configuration = new ExtensionConfiguration();
        $this->assertEquals(0, $configuration->getDefaultTimeout());
    }

    /**
     * @test
     */
    public function getBanTimeoutShouldReturnInteger()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['varnish'] = ['ban_timeout' => '10'];
        $configuration = new ExtensionConfiguration();
        $this->assertEquals(10, $configuration->getBanTimeout());
    }
}
