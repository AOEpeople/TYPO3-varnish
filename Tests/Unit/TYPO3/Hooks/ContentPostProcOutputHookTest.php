<?php
namespace Aoe\Varnish\Tests\Unit\TYPO3\Hooks;

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

use Aoe\Varnish\System\Header;
use Aoe\Varnish\TYPO3\Configuration\ExtensionConfiguration;
use Aoe\Varnish\TYPO3\Hooks\ContentPostProcOutputHook;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Cache\Backend\NullBackend;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * @covers \Aoe\Varnish\TYPO3\Hooks\ContentPostProcOutputHook
 */
class ContentPostProcOutputHookTest extends UnitTestCase
{
    /**
     * @var ContentPostProcOutputHook
     */
    private $subject;

    /**
     * @var TypoScriptFrontendController
     */
    private $frontendController;

    protected function setUp(): void
    {
        $this->subject = GeneralUtility::makeInstance(ContentPostProcOutputHook::class);
    }

    /**
     * @test
     */
    public function shouldSendAllHeader()
    {
        // mocking
        $header = $this->getHeaderMock(1, 2, 1);
        $this->getFrontendControllerMock('1');

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['varnish'] = ['debug' => 1];

        $hookReflection = new \ReflectionClass($this->subject);
        $reflectionPropertyHeader = $hookReflection->getProperty('header');
        $reflectionPropertyHeader->setAccessible(true);
        $reflectionPropertyHeader->setValue($this->subject, $header);

        // execute
        $this->subject->sendHeader([], $this->frontendController);
    }

    /**
     * @test
     */
    public function shouldNotSendDebugHeader()
    {
        // mocking
        $header = $this->getHeaderMock(1, 2, 0);

        $this->getFrontendControllerMock('1');

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['varnish'] = ['debug' => 0];

        $hookReflection = new \ReflectionClass($this->subject);
        $reflectionPropertyHeader = $hookReflection->getProperty('header');
        $reflectionPropertyHeader->setAccessible(true);
        $reflectionPropertyHeader->setValue($this->subject, $header);

        // execute
        $this->subject->sendHeader([], $this->frontendController);
    }

    /**
     * @test
     */
    public function shouldNotSendVarnishEnabledHeader()
    {
        // mocking
        $header = $this->getHeaderMock(0, 2, 1);

        $this->getFrontendControllerMock('0');

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['varnish'] = ['debug' => 1];

        $hookReflection = new \ReflectionClass($this->subject);
        $reflectionPropertyHeader = $hookReflection->getProperty('header');
        $reflectionPropertyHeader->setAccessible(true);
        $reflectionPropertyHeader->setValue($this->subject, $header);

        // execute
        $this->subject->sendHeader([], $this->frontendController);
    }

    /**
     * @param int $sendEnabledHeaderCallingCount
     * @param int $sendHeaderForTagCallingCount
     * @param int $sendDebugHeaderCallingCount
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getHeaderMock(
        $sendEnabledHeaderCallingCount,
        $sendHeaderForTagCallingCount,
        $sendDebugHeaderCallingCount
    ) {
        $header = $this->getMockBuilder(Header::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendEnabledHeader', 'sendHeaderForTag', 'sendDebugHeader'])
            ->getMock();

        $header->expects($this->exactly($sendEnabledHeaderCallingCount))
            ->method('sendEnabledHeader');

        $header->expects($this->exactly($sendHeaderForTagCallingCount))
            ->method('sendHeaderForTag');

        $header->expects($this->exactly($sendDebugHeaderCallingCount))
            ->method('sendDebugHeader');

        return $header;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $object
     * @param int $pageId
     * @param string $varnishCacheEnabled
     */
    private function setTypoScriptFrontendControllerReflectionProperties(
        \PHPUnit_Framework_MockObject_MockObject $object,
        $pageId,
        $varnishCacheEnabled
    ) {
        $reflection = new \ReflectionClass($object);
        $reflectionPropertyId = $reflection->getProperty('id');
        $reflectionPropertyId->setAccessible(true);
        $reflectionPropertyId->setValue($this->frontendController, $pageId);

        $reflectionPropertyId = $reflection->getProperty('page');
        $reflectionPropertyId->setAccessible(true);
        $reflectionPropertyId->setValue(
            $this->frontendController,
            ['varnish_cache' => $varnishCacheEnabled]
        );
    }

    private function getFrontendControllerMock(string $vanishCacheEnabled): void
    {
        $this->frontendController = $this
            ->getMockBuilder(TypoScriptFrontendController::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->setTypoScriptFrontendControllerReflectionProperties(
            $this->frontendController,
            12345,
            $vanishCacheEnabled
        );
    }
}
