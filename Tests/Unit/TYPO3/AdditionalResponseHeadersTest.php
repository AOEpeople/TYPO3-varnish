<?php
namespace Aoe\Varnish\Tests\Unit\TYPO3;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 AOE GmbH <dev@aoe.com>
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

use Aoe\Varnish\System\Header;
use Aoe\Varnish\TYPO3\AdditionalResponseHeaders;
use Aoe\Varnish\TYPO3\Configuration\ExtensionConfiguration;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * @covers \Aoe\Varnish\TYPO3\AdditionalResponseHeaders
 */
class AdditionalResponseHeadersTest extends UnitTestCase
{
    /**
     * @test
     */
    public function shouldSendAllHeader()
    {
        // mocking
        $extensionConfigurationMock = $this->createExtensionConfigurationMock(true);
        $headerMock = $this->createHeaderMock(1, 2, 1);

        $requestMock = $this->createRequestMock(true);
        $handlerMock = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $handlerMock->expects(self::once())->method('handle');

        // execute
        $subject = new AdditionalResponseHeaders($extensionConfigurationMock, $headerMock);
        $subject->process($requestMock, $handlerMock);
    }

    /**
     * @test
     */
    public function shouldNotSendDebugHeader()
    {
        // mocking
        $extensionConfigurationMock = $this->createExtensionConfigurationMock(false);
        $headerMock = $this->createHeaderMock(1, 2, 0);

        $requestMock = $this->createRequestMock(true);
        $handlerMock = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $handlerMock->expects(self::once())->method('handle');

        // execute
        $subject = new AdditionalResponseHeaders($extensionConfigurationMock, $headerMock);
        $subject->process($requestMock, $handlerMock);
    }

    /**
     * @test
     */
    public function shouldNotSendVarnishEnabledHeader()
    {
        // mocking
        $extensionConfigurationMock = $this->createExtensionConfigurationMock(true);
        $headerMock = $this->createHeaderMock(0, 2, 1);

        $requestMock = $this->createRequestMock(false);
        $handlerMock = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $handlerMock->expects(self::once())->method('handle');

        // execute
        $subject = new AdditionalResponseHeaders($extensionConfigurationMock, $headerMock);
        $subject->process($requestMock, $handlerMock);
    }

    /**
     * @param boolean $isDebugEnabled
     * @return ExtensionConfiguration|MockObject
     */
    private function createExtensionConfigurationMock($isDebugEnabled)
    {
        $extensionConfigurationMock = $this->getMockBuilder(ExtensionConfiguration::class)->disableOriginalConstructor()->getMock();
        $extensionConfigurationMock->expects($this->once())->method('isDebug')->willReturn($isDebugEnabled);
        return $extensionConfigurationMock;
    }

    /**
     * @param integer $sendEnabledHeaderCallingCount
     * @param integer $sendHeaderForTagCallingCount
     * @param integer $sendDebugHeaderCallingCount
     * @return MockObject
     */
    private function createHeaderMock(
        int $sendEnabledHeaderCallingCount,
        int $sendHeaderForTagCallingCount,
        int $sendDebugHeaderCallingCount
    ) {
        $header = $this->getMockBuilder(Header::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendEnabledHeader', 'sendHeaderForTag', 'sendDebugHeader'])
            ->getMock();
        $header->expects($this->exactly($sendEnabledHeaderCallingCount))->method('sendEnabledHeader');
        $header->expects($this->exactly($sendHeaderForTagCallingCount))->method('sendHeaderForTag');
        $header->expects($this->exactly($sendDebugHeaderCallingCount))->method('sendDebugHeader');
        return $header;
    }

    /**
     * @param boolean $isVanishCacheEnabled
     * @return ServerRequestInterface MockObject
     */
    private function createRequestMock(bool $isVanishCacheEnabled)
    {
        $frontendController = $this
            ->getMockBuilder(TypoScriptFrontendController::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->setTypoScriptFrontendControllerReflectionProperties(
            $frontendController,
            12345,
            $isVanishCacheEnabled
        );

        $requestMock = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $requestMock
            ->expects($this->once())
            ->method('getAttribute')
            ->with('frontend.controller')
            ->willReturn($frontendController);
        return $requestMock;
    }

    /**
     * @param MockObject $object
     * @param integer $pageId
     * @param boolean $isVanishCacheEnabled
     */
    private function setTypoScriptFrontendControllerReflectionProperties(
        MockObject $frontendController,
        int $pageId,
        bool $isVanishCacheEnabled
    ) {
        $reflection = new \ReflectionClass($frontendController);
        $reflectionPropertyId = $reflection->getProperty('id');
        $reflectionPropertyId->setAccessible(true);
        $reflectionPropertyId->setValue($frontendController, $pageId);

        $reflectionPropertyId = $reflection->getProperty('page');
        $reflectionPropertyId->setAccessible(true);
        $reflectionPropertyId->setValue(
            $frontendController,
            ['varnish_cache' => $isVanishCacheEnabled ? '1' : '0']
        );
    }
}
