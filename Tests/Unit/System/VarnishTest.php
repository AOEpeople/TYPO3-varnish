<?php
namespace Aoe\Varnish\System;

use Aoe\Varnish\Domain\Model\TagInterface;
use Aoe\Varnish\TYPO3\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;

/**
 * @covers Aoe\Varnish\System\Varnish
 */
class VarnishTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Varnish
     */
    private $varnish;

    /**
     * @var Http|\PHPUnit_Framework_MockObject_MockObject
     */
    private $http;

    /**
     * @var ExtensionConfiguration|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionConfiguration;

    /**
     * @var LogManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $logManager;

    public function setUp()
    {
        $this->http = $this->getMockBuilder(Http::class)
            ->setMethods(array('request', 'wait'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->extensionConfiguration = $this->getMockBuilder(ExtensionConfiguration::class)
            ->setMethods(array('getHosts'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->extensionConfiguration
            ->expects($this->any())
            ->method('getHosts')
            ->will($this->returnValue(['domain.tld']));

        $this->logManager = $this->getMockBuilder(LogManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLogger'])
            ->getMock();

        $this->varnish = new Varnish($this->http, $this->extensionConfiguration, $this->logManager);
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1435159558
     */
    public function banByTagShouldThrowExceptionOnInvalidTag()
    {
        $tag = $this->getMockBuilder(TagInterface::class)
            ->setMethods(array('isValid', 'getIdentifier'))
            ->getMock();
        $tag->expects($this->once())->method('isValid')->will($this->returnValue(false));
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
            ['X-Ban-Tags' => 'my_identifier']
        );
        /** @var TagInterface|\PHPUnit_Framework_MockObject_MockObject $tag */
        $tag = $this->getMockBuilder(TagInterface::class)
            ->setMethods(array('isValid', 'getIdentifier'))
            ->getMock();
        $tag->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $tag->expects($this->once())->method('getIdentifier')->will($this->returnValue('my_identifier'));
        $this->varnish->banByTag($tag);
    }

    /**
     * @test
     */
    public function banAllShouldCallHttpCorrectly()
    {
        $this->http->expects($this->once())->method('request')->with('BAN', 'domain.tld', ['X-Ban-All' => '1']);
        $this->varnish->banAll();
    }

    /**
     * @test
     */
    public function shouldLogOnShutdown()
    {
        $this->http->expects($this->once())->method('wait')->will($this->returnValue([
            ['success' => true, 'reason' => 'banned all'],
            ['success' => false, 'reason' => 'failed!']
        ]));

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->setMethods(['info', 'alert'])
            ->getMock();
        $logger->expects($this->once())->method('info')->with('banned all');
        $logger->expects($this->once())->method('alert')->with('failed!');

        $this->logManager->expects($this->any())->method('getLogger')
            ->will($this->returnValue($logger));

        $this->varnish->shutdown();
    }
}
