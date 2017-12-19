<?php
namespace Aoe\Varnish\Tests\Unit\Domain\Model\Tag;

use Aoe\Varnish\Domain\Model\Tag\PageTag;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * @covers \Aoe\Varnish\Domain\Model\Tag\PageTag
 */
class PageTagTest extends UnitTestCase
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
