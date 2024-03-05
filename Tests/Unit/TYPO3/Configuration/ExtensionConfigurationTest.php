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
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as Typo3ExtensionConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

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
    public function testIsDebugShouldReturnTrue(): void
    {
        $this->assertTrue($this->createConfiguration('debug', 1)->isDebug());
    }

    /**
     * @test
     */
    public function testIsDebugShouldReturnFalse(): void
    {
        $this->assertFalse($this->createConfiguration('debug', 0)->isDebug());
    }

    /**
     * @test
     */
    public function testGetHostsShouldSingleHost(): void
    {
        $this->assertSame(
            ['http://www.aoe.com'],
            $this->createConfiguration('hosts', 'www.aoe.com')
                ->getHosts()
        );
    }

    /**
     * @test
     */
    public function testGetHostsShouldMultipleHost(): void
    {
        $this->assertSame(
            ['http://www.aoe.com', 'http://test.aoe.com', 'http://test1.aoe.com'],
            $this->createConfiguration('hosts', 'www.aoe.com,test.aoe.com,test1.aoe.com')
                ->getHosts()
        );
    }

    /**
     * @test
     */
    public function testGetDefaultTimeoutShouldReturnInteger(): void
    {
        $this->assertSame(0, $this->createConfiguration('default_timeout', '0')->getDefaultTimeout());
    }

    /**
     * @test
     */
    public function testGetBanTimeoutShouldReturnInteger(): void
    {
        $this->assertSame(10, $this->createConfiguration('ban_timeout', '10')->getBanTimeout());
    }

    /**
     * @param string|integer $key
     * @param string|integer $value
     */
    private function createConfiguration($key, $value): ExtensionConfiguration
    {
        $defaultConfig = [];
        $defaultConfig[$key] = $value;

        $typo3ExtensionConfiguration = $this->getMockBuilder(Typo3ExtensionConfiguration::class)->getMock();
        $typo3ExtensionConfiguration
            ->expects(self::once())
            ->method('get')
            ->with('varnish')
            ->willReturn($defaultConfig);
        return new ExtensionConfiguration($typo3ExtensionConfiguration);
    }
}
