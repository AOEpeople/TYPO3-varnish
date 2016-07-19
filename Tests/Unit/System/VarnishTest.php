<?php
namespace Aoe\Varnish\System;

use Aoe\Varnish\Domain\Model\TagInterface;

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

    public function setUp()
    {
        $this->http = $this->getMockBuilder('Aoe\\Varnish\\System\\Http')
            ->setMethods(array('addCommand', '__destruct'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->varnish = new Varnish($this->http);
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1435159558
     */
    public function banByTagShouldThrowExceptionOnInvalidTag()
    {
        $tag = $this->getMockBuilder('Aoe\\Varnish\\Domain\\Model\\TagInterface')
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
            'www.congstar.local',
            'X-Ban-Tags:my_identifier'
        );
        $tag = $this->getMockBuilder('Aoe\\Varnish\\Domain\\Model\\TagInterface')
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
        $http->expects($this->once())->method('addCommand')->with('BAN', 'www.congstar.local', 'X-Ban-All:1');
        $this->varnish->banAll();
    }
}
