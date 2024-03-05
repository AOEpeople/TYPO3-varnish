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
use Aoe\Varnish\Domain\Model\Tag\PageTag;
use Aoe\Varnish\System\Varnish;
use Aoe\Varnish\TYPO3\Hooks\TceMainHook;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Cache\Backend\NullBackend;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Aoe\Varnish\TYPO3\Hooks\TceMainHook
 */
class TceMainHookTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    /**
     * @var Varnish
     */
    private MockObject $varnish;

    /**
     * @var TceMainHook
     */
    private MockObject $tceMainHook;

    /**
     * @var DataHandler
     */
    private MockObject $dataHandler;

    /**
     * initialize objects
     */
    protected function setUp(): void
    {
        /** https://github.com/TYPO3/TYPO3.CMS/blob/master/typo3/sysext/backend/Tests/Unit/Utility/BackendUtilityTest.php#L1044-L1053 */
        $cacheConfigurations = [
            'runtime' => [
                'backend' => NullBackend::class,
            ],
        ];
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $cacheManager->setCacheConfigurations($cacheConfigurations);

        $this->dataHandler = $this->getMockBuilder(DataHandler::class)->getMock();

        /** @var BackendUserAuthentication $beUser */
        $beUser = $this->getMockBuilder(BackendUserAuthentication::class)->getMock();
        $beUser->workspace = 0;
        $this->dataHandler->BE_USER = $beUser;

        $this->varnish = $this->getMockBuilder(Varnish::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['banByTag', 'banAll'])
            ->getMock();

        $this->tceMainHook = $this->createPartialMock(TceMainHook::class, ['getVarnish']);
        $this->tceMainHook->method('getVarnish')
            ->willReturn($this->varnish);
    }

    /**
     * @test
     */
    public function testShouldBanAllTYPO3PagesIfCacheCmdIsPages(): void
    {
        $expectedTag = new PageTag();

        /** @var MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects(self::once())
            ->method('banByTag')
            ->with($expectedTag);

        $this->tceMainHook->clearCachePostProc(
            ['cacheCmd' => 'pages'],
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function testShouldBanByTagIfPidGivenAsCacheCmd(): void
    {
        $expectedTag = new PageIdTag(4711);

        /** @var MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects(self::once())
            ->method('banByTag')
            ->with($expectedTag);

        $this->tceMainHook->clearCachePostProc(
            ['cacheCmd' => 4711],
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function testShouldNOTBanByTagIfPidGivenAsCacheCmdAndPageIdIsZero(): void
    {
        /** @var MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects(self::never())
            ->method('banByTag');

        $this->tceMainHook->clearCachePostProc(
            ['cacheCmd' => 0],
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function testShouldNOTBanByTagIfPidGivenAsCacheCmdAndPageIdIsNegative(): void
    {
        /** @var MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects(self::never())
            ->method('banByTag');

        $this->tceMainHook->clearCachePostProc(
            ['cacheCmd' => -1],
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function testShouldBanByTagIfPidGivenAsUidPage(): void
    {
        $expectedTag = new PageIdTag(4712);

        /** @var MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects(self::once())
            ->method('banByTag')
            ->with($expectedTag);

        $this->tceMainHook->clearCachePostProc(
            ['uid_page' => 4712],
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function testShouldNOTBanByTagIfPidGivenAsUidPageAndPageIdIsZero(): void
    {
        /** @var MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects(self::never())
            ->method('banByTag');

        $this->tceMainHook->clearCachePostProc(
            ['uid_page' => 0],
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function testShouldNOTBanByTagIfPidGivenAsUidPageAndPageIdIsNegative(): void
    {
        /** @var MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects(self::never())
            ->method('banByTag');

        $this->tceMainHook->clearCachePostProc(
            ['uid_page' => -1],
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function testShouldBanByTagIfPidGivenWithTablePages(): void
    {
        $expectedTag = new PageIdTag(4713);

        /** @var MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects(self::once())
            ->method('banByTag')
            ->with($expectedTag);

        $this->tceMainHook->clearCachePostProc(
            ['table' => 'pages', 'uid' => 4713],
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function testShouldBanByPageIdTagOnlyOnce(): void
    {
        $expectedTag = new PageIdTag(4714);

        /** @var MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects(self::once())
            ->method('banByTag')
            ->with($expectedTag);

        $this->tceMainHook->clearCachePostProc(
            ['table' => 'pages', 'uid' => 4714],
            $this->dataHandler
        );
        $this->tceMainHook->clearCachePostProc(
            ['table' => 'pages', 'uid' => 4714],
            $this->dataHandler
        );
        $this->tceMainHook->clearCachePostProc(
            ['table' => 'pages', 'uid' => 4714],
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function testShouldNOTBanByTagIfPidGivenWithTablePagesAndPageIdIsZero(): void
    {
        /** @var MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects(self::never())
            ->method('banByTag');

        $this->tceMainHook->clearCachePostProc(
            ['table' => 'pages', 'uid' => 0],
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function testShouldNOTBanByTagIfPidGivenWithTablePagesAndPageIdIsNegative(): void
    {
        /** @var MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects(self::never())
            ->method('banByTag');

        $this->tceMainHook->clearCachePostProc(
            ['table' => 'pages', 'uid' => -1],
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function testShouldNOTBanByTagIfPidGivenWithOtherTableThanPages(): void
    {
        /** @var MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects(self::never())
            ->method('banByTag');

        $this->tceMainHook->clearCachePostProc(
            ['table' => 'fe_users', 'uid' => 1],
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function testShouldNOTBanByTagIfBeUserIsInWorkspace(): void
    {
        $this->dataHandler->BE_USER->workspace = 1;

        /** @var MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects(self::never())
            ->method('banByTag');

        $this->tceMainHook->clearCachePostProc(
            ['cacheCmd' => 4715],
            $this->dataHandler
        );
    }
}
