<?php
namespace Aoe\Varnish\System;

use Aoe\Varnish\Domain\Model\TagInterface;
use Aoe\Varnish\TYPO3\Configuration\ExtensionConfiguration;

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
     * @var Http
     */
    private $http;

    /**
     * @var ExtensionConfiguration
     */
    private $extensionConfiguration;

    public function setUp()
    {
        $this->http = $this->getMockBuilder(Http::class)
            ->setMethods(array('addCommand', '__destruct'))
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
        $this->varnish = new Varnish($this->http, $this->extensionConfiguration);
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
        /** @var \PHPUnit_Framework_MockObject_MockObject $http */
        $http = $this->http;
        $http->expects($this->once())->method('addCommand')->with(
            'BAN',
            'domain.tld',
            'X-Ban-Tags:my_identifier'
        );
        $tag = $this->getMockBuilder(TagInterface::class)
            ->setMethods(array('isValid', 'getIdentifier'))
            ->getMock();
        $tag->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $tag->expects($this->once())->method('getIdentifier')->will($this->returnValue('my_identifier'));
        /** @var TagInterface $tag */
        $this->varnish->banByTag($tag);
    }

    /**
     * @test
     */
    public function banAllShouldCallHttpCorrectly()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject $http */
        $http = $this->http;
        $http->expects($this->once())->method('addCommand')->with('BAN', 'domain.tld', 'X-Ban-All:1');
        $this->varnish->banAll();
    }
}
