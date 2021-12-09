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
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Cache\Backend\NullBackend;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @covers \Aoe\Varnish\TYPO3\Hooks\TceMainHook
 */
class TceMainHookTest extends UnitTestCase
{
    /**
     * @var Varnish
     */
    private $varnish;

    /**
     * @var TceMainHook
     */
    private $tceMainHook;

    /**
     * @var DataHandler
     */
    private $dataHandler;

    /**
     * initialize objects
     */
    public function setUp(): void
    {
        /// https://github.com/TYPO3/TYPO3.CMS/blob/master/typo3/sysext/backend/Tests/Unit/Utility/BackendUtilityTest.php#L1044-L1053
        $cacheConfigurations = [
            'runtime' => [
                'backend' => NullBackend::class
            ]
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
            ->setMethods(array('banByTag', 'banAll'))
            ->getMock();

        $this->tceMainHook = $this->createPartialMock(TceMainHook::class, ['getVarnish']);
        $this->tceMainHook->expects($this->any())->method('getVarnish')->willReturn($this->varnish);
    }

    /**
     * @test
     */
    public function shouldBanAllTYPO3PagesIfCacheCmdIsPages()
    {
        $expectedTag = new PageTag();

        /** @var \PHPUnit\Framework\MockObject\MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects($this->once())->method('banByTag')->with($expectedTag);

        $this->tceMainHook->clearCachePostProc(
            array('cacheCmd' => 'pages'),
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function shouldBanByTagIfPidGivenAsCacheCmd()
    {
        $expectedTag = new PageIdTag(4711);

        /** @var \PHPUnit\Framework\MockObject\MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects($this->once())->method('banByTag')->with($expectedTag);

        $this->tceMainHook->clearCachePostProc(
            array('cacheCmd' => 4711),
            $this->dataHandler
        );
    }

    /**
     */
    public function shouldBanAllTypo3PagesWhenCacheCmdIsPages()
    {
        $varnish = $this->varnish;
        $varnish->expects($this->once())->method('banByTag')->with("");
    }

    /**
     * @test
     */
    public function shouldNOTBanByTagIfPidGivenAsCacheCmdAndPageIdIsZero()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects($this->never())->method('banByTag');

        $this->tceMainHook->clearCachePostProc(
            array('cacheCmd' => 0),
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function shouldNOTBanByTagIfPidGivenAsCacheCmdAndPageIdIsNegative()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects($this->never())->method('banByTag');

        $this->tceMainHook->clearCachePostProc(
            array('cacheCmd' => -1),
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function shouldBanByTagIfPidGivenAsUidPage()
    {
        $expectedTag = new PageIdTag(4712);

        /** @var \PHPUnit\Framework\MockObject\MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects($this->once())->method('banByTag')->with($expectedTag);

        $this->tceMainHook->clearCachePostProc(
            array('uid_page' => 4712),
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function shouldNOTBanByTagIfPidGivenAsUidPageAndPageIdIsZero()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects($this->never())->method('banByTag');

        $this->tceMainHook->clearCachePostProc(
            array('uid_page' => 0),
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function shouldNOTBanByTagIfPidGivenAsUidPageAndPageIdIsNegative()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects($this->never())->method('banByTag');

        $this->tceMainHook->clearCachePostProc(
            array('uid_page' => -1),
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function shouldBanByTagIfPidGivenWithTablePages()
    {
        $expectedTag = new PageIdTag(4713);

        /** @var \PHPUnit\Framework\MockObject\MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects($this->once())->method('banByTag')->with($expectedTag);

        $this->tceMainHook->clearCachePostProc(
            array('table' => 'pages', 'uid' => 4713),
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function shouldNOTBanByTagIfPidGivenWithTablePagesAndPageIdIsZero()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects($this->never())->method('banByTag');

        $this->tceMainHook->clearCachePostProc(
            array('table' => 'pages', 'uid' => 0),
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function shouldNOTBanByTagIfPidGivenWithTablePagesAndPageIdIsNegative()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects($this->never())->method('banByTag');

        $this->tceMainHook->clearCachePostProc(
            array('table' => 'pages', 'uid' => -1),
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function shouldNOTBanByTagIfPidGivenWithOtherTableThanPages()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects($this->never())->method('banByTag');

        $this->tceMainHook->clearCachePostProc(
            array('table' => 'fe_users', 'uid' => 1),
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function shouldNOTBanByTagIfBeUserIsInWorkspace()
    {
        $this->dataHandler->BE_USER->workspace = 1;

        /** @var \PHPUnit\Framework\MockObject\MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects($this->never())->method('banByTag');

        $this->tceMainHook->clearCachePostProc(
            array('cacheCmd' => 4715),
            $this->dataHandler
        );
    }
}
