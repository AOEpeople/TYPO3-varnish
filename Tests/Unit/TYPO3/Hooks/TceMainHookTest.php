<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 AOE GmbH <dev@aoe.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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

namespace AOE\Varnish\TYPO3\Hooks;

use AOE\Varnish\Domain\Model\Tag\PageTag;
use AOE\Varnish\System\Varnish;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use Zend\Server\Reflection\ReflectionClass;

/**
 * @package AOE\Varnish
 * @covers AOE\Varnish\TYPO3\Hooks\TceMainHook
 */
class TceMainHookTest extends \PHPUnit_Framework_TestCase
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
    public function setUp()
    {
        $this->dataHandler = $this->getMock('TYPO3\\CMS\\Core\\DataHandling\\DataHandler');
        $this->varnish = $this->getMockBuilder('AOE\\Varnish\\System\\Varnish')
            ->disableOriginalConstructor()
            ->setMethods(array('banByTag', 'banAll'))
            ->getMock();
        $objectManager = $this->getMockBuilder('TYPO3\\CMS\\Extbase\\Object\\ObjectManagerInterface')
            ->setMethods(array('isRegistered', 'get', 'create', 'getEmptyObject', 'getScope'))
            ->getMock();

        $objectManager->expects($this->any())
            ->method('get')
            ->with('AOE\\Varnish\\System\\Varnish')
            ->will($this->returnValue($this->varnish));

        $this->tceMainHook = new TceMainHook();
        /** @var ObjectManagerInterface $objectManager */
        $this->tceMainHook->injectObjectManager($objectManager);
    }

    /**
     * @test
     */
    public function shouldBanByTagIfPidGivenAsCacheCmd()
    {
        $expectedTag = new PageTag();
        $expectedTag->setPageId(4711);

        /** @var \PHPUnit_Framework_MockObject_MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects($this->once())->method('banByTag')->with($expectedTag);

        $this->tceMainHook->clearCachePostProc(
            array('cacheCmd' => 4711),
            $this->dataHandler
        );
    }

    /**
     * @test
     */
    public function shouldNOTBanByTagIfPidGivenAsCacheCmdAndPageIdIsZero()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject $varnish */
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
        /** @var \PHPUnit_Framework_MockObject_MockObject $varnish */
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
        $expectedTag = new PageTag();
        $expectedTag->setPageId(4712);

        /** @var \PHPUnit_Framework_MockObject_MockObject $varnish */
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
        /** @var \PHPUnit_Framework_MockObject_MockObject $varnish */
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
        /** @var \PHPUnit_Framework_MockObject_MockObject $varnish */
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
        $expectedTag = new PageTag();
        $expectedTag->setPageId(4713);

        /** @var \PHPUnit_Framework_MockObject_MockObject $varnish */
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
        /** @var \PHPUnit_Framework_MockObject_MockObject $varnish */
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
        /** @var \PHPUnit_Framework_MockObject_MockObject $varnish */
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
        /** @var \PHPUnit_Framework_MockObject_MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects($this->never())->method('banByTag');

        $this->tceMainHook->clearCachePostProc(
            array('table' => 'fe_users', 'uid' => 1),
            $this->dataHandler
        );
    }
}
