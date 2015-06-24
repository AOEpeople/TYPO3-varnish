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

namespace AOE\Varnish\System;

use AOE\Varnish\Domain\Model\TagInterface;

/**
 * @package AOE\Varnish
 * @covers AOE\Varnish\System\Varnish
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
     * initialize objects
     */
    public function setUp() {
        $this->http = $this->getMockBuilder('AOE\\Varnish\\System\\Http')
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
        $tag = $this->getMockBuilder('AOE\\Varnish\\Domain\\Model\\TagInterface')
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
        $http->expects($this->once())->method('addCommand')->with('BAN', 'www.congstar.local', 'X-Ban-Tags:my_identifier');
        $tag = $this->getMockBuilder('AOE\\Varnish\\Domain\\Model\\TagInterface')
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
