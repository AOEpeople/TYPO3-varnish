<?php
namespace Aoe\Varnish\Domain\Model\Tag;

/**
 * @covers \Aoe\Varnish\Domain\Model\Tag\PageTag
 */
class PageTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function tagShouldBeValid()
    {
        $tag = new PageTag();
        $this->assertTrue($tag->isValid());
    }

    /**
     * @test
     */
    public function shouldGetIdentifier()
    {
        $tag = new PageTag();
        $this->assertEquals('typo3_page', $tag->getIdentifier());
    }
}
