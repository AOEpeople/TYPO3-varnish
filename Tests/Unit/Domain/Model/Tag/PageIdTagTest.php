<?php
namespace Aoe\Varnish\Domain\Model\Tag;

/**
 * @covers Aoe\Varnish\Domain\Model\Tag\PageTag
 */
class PageIdTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function isValidShouldFailWithNegativePageId()
    {
        $tag = new PageIdTag(-1);
        $this->assertFalse($tag->isValid());
    }

    /**
     * @test
     */
    public function isValidShouldFailWithPageIdZero()
    {
        $tag = new PageIdTag(0);
        $this->assertFalse($tag->isValid());
    }

    /**
     * @test
     */
    public function isValidShouldFailWithPageIdNotNumeric()
    {
        $tag = new PageIdTag('string');
        $this->assertFalse($tag->isValid());
    }

    /**
     * @test
     */
    public function isValidWithInteger()
    {
        $tag = new PageIdTag(11);
        $this->assertTrue($tag->isValid());
    }

    /**
     * @test
     */
    public function getIdentifier()
    {
        $tag = new PageIdTag(11);
        $this->assertEquals('page_11', $tag->getIdentifier());
    }
}
