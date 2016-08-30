<?php
namespace Aoe\Varnish\Domain\Model\Tag;

/**
 * @covers Aoe\Varnish\Domain\Model\Tag\PageTag
 */
class PageTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function tagShouldNotBeValidWithIntegerValue()
    {
        $tag = new PageTag(1);
        $this->assertFalse($tag->isValid());
    }

    /**
     * @test
     */
    public function tagShouldBeValidWithStringValueTypo3Page()
    {
        $tag = new PageTag('typo3_page');
        $this->assertTrue($tag->isValid());
    }

    /**
     * @test
     */
    public function shouldGetIdentifier()
    {
        $tag = new PageTag('pages');
        $this->assertEquals('pages', $tag->getIdentifier());
    }
}
