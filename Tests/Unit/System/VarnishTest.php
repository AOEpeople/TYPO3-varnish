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
use Nimut\TestingFramework\TestCase\UnitTestCase;
use RuntimeException;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;

/**
 * @covers \Aoe\Varnish\System\Varnish
 */
class VarnishTest extends UnitTestCase
{
    /**
     * @var Varnish
     */
    private $varnish;

    /**
     * @var Http|\PHPUnit\Framework\MockObject\MockObject
     */
    private $http;

    /**
     * @var ExtensionConfiguration|\PHPUnit\Framework\MockObject\MockObject
     */
    private $extensionConfiguration;

    /**
     * @var LogManager|\PHPUnit\Framework\MockObject\MockObject
     */
    private $logManager;

    public function setUp(): void
    {
        $this->http = $this->getMockBuilder(Http::class)
            ->setMethods(array('request', 'wait'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->extensionConfiguration = $this->getMockBuilder(ExtensionConfiguration::class)
            ->setMethods(['getHosts', 'getBanTimeout', 'getDefaultTimeout'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->extensionConfiguration
            ->expects($this->any())
            ->method('getHosts')
            ->willReturn(['domain.tld']);
        $this->extensionConfiguration
            ->expects($this->any())
            ->method('getBanTimeout')
            ->willReturn(10);
        $this->extensionConfiguration
            ->expects($this->any())
            ->method('getDefaultTimeout')
            ->willReturn(0);

        $this->logManager = $this->getMockBuilder(LogManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLogger'])
            ->getMock();

        $this->varnish = new Varnish($this->http, $this->extensionConfiguration, $this->logManager);
    }

    /**
     * @test
     */
    public function banByTagShouldThrowExceptionOnInvalidTag()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(1435159558);

        $tag = $this->getMockBuilder(TagInterface::class)
            ->setMethods(array('isValid', 'getIdentifier'))
            ->getMock();
        $tag->expects($this->once())->method('isValid')->willReturn(false);
        /** @var TagInterface $tag */
        $this->varnish->banByTag($tag);
    }

    /**
     * @test
     */
    public function banByTagShouldCallHttpCorrectly()
    {
        $this->http->expects($this->once())->method('request')->with(
            'BAN',
            'domain.tld',
            ['X-Ban-Tags' => 'my_identifier'],
            10
        );
        /** @var TagInterface|\PHPUnit\Framework\MockObject\MockObject $tag */
        $tag = $this->getMockBuilder(TagInterface::class)
            ->setMethods(array('isValid', 'getIdentifier'))
            ->getMock();
        $tag->expects($this->once())->method('isValid')->willReturn(true);
        $tag->expects($this->once())->method('getIdentifier')->willReturn('my_identifier');
        $this->varnish->banByTag($tag);
    }

    /**
     * @test
     */
    public function banAllShouldCallHttpCorrectly()
    {
        $this->http->expects($this->once())->method('request')->with('BAN', 'domain.tld', ['X-Ban-All' => '1'], 10);
        $this->varnish->banAll();
    }

    /**
     * @test
     */
    public function banByRegexShouldCallHttpCorrectly()
    {
        $this->http
            ->expects($this->once())
            ->method('request')
            ->with('BAN', 'domain.tld', ['X-Ban-Regex' => '/*']);
        $this->varnish->banByRegex('/*');
    }
    
    /**
     * @test
     */
    public function shouldLogOnShutdown()
    {
        $this->http->expects($this->once())->method('wait')->willReturn([
            ['success' => true, 'reason' => 'banned all'],
            ['success' => false, 'reason' => 'failed!']
        ]);

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->setMethods(['info', 'alert'])
            ->getMock();
        $logger->expects($this->once())->method('info')->with('banned all');
        $logger->expects($this->once())->method('alert')->with('failed!');

        $this->logManager->expects($this->any())->method('getLogger')
            ->willReturn($logger);

        $this->varnish->shutdown();
    }
}
