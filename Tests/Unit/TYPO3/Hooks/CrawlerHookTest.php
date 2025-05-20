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

use Aoe\Varnish\Domain\Model\Tag\PageIdTag;
use Aoe\Varnish\EventListener\Crawler;
use Aoe\Varnish\System\Varnish;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CrawlerHookTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    private MockObject&Varnish $varnish;

    private MockObject&Crawler $crawlerEvent;

    public function testShouldClearVarnishCache(): void
    {
        $this->initializeTest(true);

        $pageId = 123456;
        $this->varnish->expects($this->once())
            ->method('banByTag')
            ->with(new PageIdTag($pageId));

        $tsfe = $this->createTsfeMock($pageId, true);
        $this->crawlerEvent->clearVarnishCache($tsfe);
    }

    public function testShouldNotClearVarnishCacheWhenCrawlerExtensionIsNotLoaded(): void
    {
        $this->initializeTest(false);

        $pageId = 123456;
        $this->varnish->expects($this->never())
            ->method('banByTag');

        $tsfe = $this->createTsfeMock($pageId, false);
        $this->crawlerEvent->clearVarnishCache($tsfe);
    }

    public function testShouldNotClearVarnishCacheWhenCrawlerIsNotRunning(): void
    {
        $this->initializeTest(true);

        $pageId = 123456;
        $this->varnish->expects($this->never())
            ->method('banByTag');

        $tsfe = $this->createTsfeMock($pageId, false);
        $this->crawlerEvent->clearVarnishCache($tsfe);
    }

    private function createTsfeMock(int $pageId, bool $isCrawlerRunning): TypoScriptFrontendController
    {
        /** @var TypoScriptFrontendController $tsfe */
        $tsfe = $this->getMockBuilder(TypoScriptFrontendController::class)->disableOriginalConstructor()->getMock();
        $tsfe->id = $pageId;
        if ($isCrawlerRunning) {
            $tsfe->applicationData['tx_crawler']['running'] = true;
        }

        return $tsfe;
    }

    private function initializeTest(bool $isCrawlerExtensionLoaded): void
    {
        $this->varnish = $this->getMockBuilder(Varnish::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['banByTag', 'banAll'])
            ->getMock();

        $this->crawlerEvent = $this->createPartialMock(Crawler::class, ['isCrawlerExtensionLoaded', 'getVarnish']);
        $this->crawlerEvent
            ->method('getVarnish')
            ->willReturn($this->varnish);
        $this->crawlerEvent
            ->method('isCrawlerExtensionLoaded')
            ->willReturn($isCrawlerExtensionLoaded);
    }
}
