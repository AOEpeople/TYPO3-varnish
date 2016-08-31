<?php
namespace Aoe\Varnish\System;

use Aoe\Varnish\Domain\Model\Tag\PageTag;

/**
 * @covers Aoe\Varnish\System\Header
 */
class HeaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Header
     */
    private $header;

    public function setUp()
    {
        $this->header = new Header();
    }

    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1435047447
     */
    public function shouldThrowRuntimeExceptionWithInvalidTag()
    {
        $tag = new PageTag(1);
        $this->header->sendHeaderForTag($tag);
    }


}
