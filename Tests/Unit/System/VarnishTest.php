<?php

namespace Aoe\Varnish\Tests\Unit\System;

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

use Aoe\Varnish\Domain\Model\TagInterface;
use Aoe\Varnish\System\Http;
use Aoe\Varnish\System\Varnish;
use Aoe\Varnish\TYPO3\Configuration\ExtensionConfiguration;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class VarnishTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    private Varnish $varnish;

    /**
     * @var Http
     */
    private MockObject $http;

    /**
     * @var ExtensionConfiguration
     */
    private MockObject $extensionConfiguration;

    /**
     * @var LogManager
     */
    private MockObject $logManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->http = $this->getMockBuilder(Http::class)
            ->onlyMethods(['request', 'wait'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->extensionConfiguration = $this->getMockBuilder(ExtensionConfiguration::class)
            ->onlyMethods(['getHosts', 'getBanTimeout', 'getDefaultTimeout'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->extensionConfiguration
            ->method('getHosts')
            ->willReturn(['domain.tld']);
        $this->extensionConfiguration
            ->method('getBanTimeout')
            ->willReturn(10);
        $this->extensionConfiguration
            ->method('getDefaultTimeout')
            ->willReturn(0);

        $this->logManager = $this->getMockBuilder(LogManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getLogger'])
            ->getMock();

        $this->varnish = new Varnish($this->http, $this->extensionConfiguration, $this->logManager);
    }

    public function testBanByTagShouldThrowExceptionOnInvalidTag(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(1_435_159_558);

        $tag = $this->getMockBuilder(TagInterface::class)
            ->onlyMethods(['isValid', 'getIdentifier'])
            ->getMock();
        $tag->expects(self::once())
            ->method('isValid')
            ->willReturn(false);
        /** @var TagInterface $tag */
        $this->varnish->banByTag($tag);
    }

    public function testBanByTagShouldCallHttpCorrectly(): void
    {
        $this->http->expects(self::once())
            ->method('request')
            ->with(
                'BAN',
                'domain.tld',
                ['X-Ban-Tags' => 'my_identifier'],
                10
            );
        /** @var TagInterface|MockObject $tag */
        $tag = $this->getMockBuilder(TagInterface::class)
            ->onlyMethods(['isValid', 'getIdentifier'])
            ->getMock();
        $tag->expects(self::once())
            ->method('isValid')
            ->willReturn(true);
        $tag->expects(self::once())
            ->method('getIdentifier')
            ->willReturn('my_identifier');
        $this->varnish->banByTag($tag);
    }

    public function testBanAllShouldCallHttpCorrectly(): void
    {
        $this->http->expects(self::once())
            ->method('request')
            ->with('BAN', 'domain.tld', ['X-Ban-All' => '1'], 10);
        $this->varnish->banAll();
    }

    public function testBanByRegexShouldCallHttpCorrectly(): void
    {
        $this->http
            ->expects(self::once())
            ->method('request')
            ->with('BAN', 'domain.tld', ['X-Ban-Regex' => '/*']);
        $this->varnish->banByRegex('/*');
    }

    public function testShouldLogOnShutdown(): void
    {
        $this->http->expects(self::once())
            ->method('wait')
            ->willReturn([
                ['success' => true, 'reason' => 'banned all'],
                ['success' => false, 'reason' => 'failed!'],
            ]);

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['info', 'alert'])
            ->getMock();
        $logger->expects(self::once())
            ->method('info')
            ->with('banned all');
        $logger->expects(self::once())
            ->method('alert')
            ->with('failed!');

        $this->logManager
            ->method('getLogger')
            ->willReturn($logger);

        $this->varnish->shutdown();
    }
}
