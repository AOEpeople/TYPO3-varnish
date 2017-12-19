<?php
namespace Aoe\Varnish\Tests\Unit\System;

use Aoe\Varnish\Domain\Model\Tag\PageIdTag;
use Aoe\Varnish\System\Header;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * @covers \Aoe\Varnish\System\Header
 */
class HeaderTest extends UnitTestCase
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
        $tag = new PageIdTag('adfasdf');
        $this->header->sendHeaderForTag($tag);
    }
}
