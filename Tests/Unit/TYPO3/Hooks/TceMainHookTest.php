<?php
namespace Aoe\Varnish\TYPO3\Hooks;

use Aoe\Varnish\Domain\Model\Tag\PageTag;
use Aoe\Varnish\System\Varnish;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * @covers Aoe\Varnish\TYPO3\Hooks\TceMainHook
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

        /** @var BackendUserAuthentication $beUser */
        $beUser = $this->getMock('TYPO3\CMS\Core\Authentication\BackendUserAuthentication');
        $beUser->workspace = 0;
        $this->dataHandler->BE_USER = $beUser;

        $this->varnish = $this->getMockBuilder('Aoe\\Varnish\\System\\Varnish')
            ->disableOriginalConstructor()
            ->setMethods(array('banByTag', 'banAll'))
            ->getMock();
        $objectManager = $this->getMockBuilder('TYPO3\\CMS\\Extbase\\Object\\ObjectManagerInterface')
            ->setMethods(array('isRegistered', 'get', 'create', 'getEmptyObject', 'getScope'))
            ->getMock();

        $objectManager->expects($this->any())
            ->method('get')
            ->with('Aoe\\Varnish\\System\\Varnish')
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
        $expectedTag = new PageTag(4711);

        /** @var \PHPUnit_Framework_MockObject_MockObject $varnish */
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
        $expectedTag = new PageTag(4712);

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
        $expectedTag = new PageTag(4713);

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

    /**
     * @test
     */
    public function shouldNOTBanByTagIfBeUserIsInWorkspace()
    {
        $this->dataHandler->BE_USER->workspace = 1;

        /** @var \PHPUnit_Framework_MockObject_MockObject $varnish */
        $varnish = $this->varnish;
        $varnish->expects($this->never())->method('banByTag');

        $this->tceMainHook->clearCachePostProc(
            array('cacheCmd' => 4715),
            $this->dataHandler
        );
    }
}
