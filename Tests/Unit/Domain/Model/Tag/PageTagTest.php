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
    public function isValidShouldFailWithNegativePageId()
    {
        $tag = new PageTag();
        $tag->setPageId(-1);
        $this->assertFalse($tag->isValid());
    }

    /**
     * @test
     */
    public function isValidShouldFailWithPageIdZero()
    {
        $tag = new PageTag();
        $tag->setPageId(0);
        $this->assertFalse($tag->isValid());
    }

    /**
     * @test
     */
    public function isValidShouldFailWithPageIdNotNumeric()
    {
        $tag = new PageTag();
        $tag->setPageId('string');
        $this->assertFalse($tag->isValid());
    }

    /**
     * @test
     */
    public function isValidWithInteger()
    {
        $tag = new PageTag();
        $tag->setPageId(11);
        $this->assertTrue($tag->isValid());
    }

    /**
     * @test
     */
    public function getIdentifier()
    {
        $tag = new PageTag();
        $tag->setPageId(11);
        $this->assertEquals('page_11', $tag->getIdentifier());
    }
}
