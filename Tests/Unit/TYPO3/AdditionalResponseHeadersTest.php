<?php

declare(strict_types=1);

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

use Aoe\Varnish\TYPO3\AdditionalResponseHeaders;
use Aoe\Varnish\TYPO3\Configuration\ExtensionConfiguration;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class AdditionalResponseHeadersTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    public function testShouldSendAllHeader(): void
    {
        // mocking
        $extensionConfigurationMock = $this->createExtensionConfigurationMock(true);

        $requestMock = $this->createRequestMock(true);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->expects($this->exactly(2))
            ->method('withAddedHeader')
            ->with('X-Tags', $this->anything())
            ->willReturnSelf();
        $responseMock->expects($this->exactly(2))
            ->method('withHeader')
            ->willReturnCallback(function (string $name, $value) use ($responseMock): \PHPUnit\Framework\MockObject\MockObject {
                static $count = 0;
                if ($count === 0) {
                    $this->assertSame('X-Debug', $name);
                    $this->assertEquals('1', $value);
                } elseif ($count === 1) {
                    $this->assertSame('X-Varnish-enabled', $name);
                    $this->assertEquals('1', $value);
                }

                $count++;
                return $responseMock;
            });

        $handlerMock = $this->createMock(RequestHandlerInterface::class);
        $handlerMock->expects($this->once())
            ->method('handle')
            ->willReturn($responseMock);

        // execute
        $subject = new AdditionalResponseHeaders($extensionConfigurationMock);
        $subject->process($requestMock, $handlerMock);
    }

    public function testShouldNotSendDebugHeader(): void
    {
        // mocking
        $extensionConfigurationMock = $this->createExtensionConfigurationMock(false);

        $requestMock = $this->createRequestMock(true);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->expects($this->exactly(2))
            ->method('withAddedHeader')
            ->willReturnSelf();
        $responseMock->expects($this->once())
            ->method('withHeader')
            ->with('X-Varnish-enabled', '1')
            ->willReturnSelf();

        $handlerMock = $this->createMock(RequestHandlerInterface::class);
        $handlerMock->expects($this->once())
            ->method('handle')
            ->willReturn($responseMock);

        // execute
        $subject = new AdditionalResponseHeaders($extensionConfigurationMock);
        $subject->process($requestMock, $handlerMock);
    }

    public function testShouldNotSendVarnishEnabledHeader(): void
    {
        // mocking
        $extensionConfigurationMock = $this->createExtensionConfigurationMock(true);

        $requestMock = $this->createRequestMock(false);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->expects($this->exactly(2))
            ->method('withAddedHeader')
            ->willReturnSelf();
        $responseMock->expects($this->once())
            ->method('withHeader')
            ->with('X-Debug', '1')
            ->willReturnSelf();

        $handlerMock = $this->createMock(RequestHandlerInterface::class);
        $handlerMock->expects($this->once())
            ->method('handle')
            ->willReturn($responseMock);

        // execute
        $subject = new AdditionalResponseHeaders($extensionConfigurationMock);
        $subject->process($requestMock, $handlerMock);
    }

    private function createExtensionConfigurationMock(bool $isDebugEnabled): MockObject
    {
        $extensionConfigurationMock = $this->createMock(ExtensionConfiguration::class);
        $extensionConfigurationMock
            ->method('isDebug')
            ->willReturn($isDebugEnabled);
        return $extensionConfigurationMock;
    }

    private function createRequestMock(bool $isVanishCacheEnabled): MockObject
    {
        $tsfe = $this->createStub(TypoScriptFrontendController::class);
        $this->setTypoScriptFrontendControllerReflectionProperties(
            $tsfe,
            12345,
            $isVanishCacheEnabled
        );

        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock
            ->expects($this->once())
            ->method('getAttribute')
            ->with('frontend.controller')
            ->willReturn($tsfe);
        return $requestMock;
    }

    private function setTypoScriptFrontendControllerReflectionProperties(
        MockObject $frontendController,
        int $pageId,
        bool $isVanishCacheEnabled
    ): void {
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
