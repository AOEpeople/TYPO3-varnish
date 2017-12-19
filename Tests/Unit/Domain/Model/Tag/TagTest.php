<?php
namespace Aoe\Varnish\Tests\Unit\Domain\Model\Tag;

use Aoe\Varnish\Domain\Model\Tag\Tag;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * @covers \Aoe\Varnish\Domain\Model\Tag\Tag
 */
class TagTest extends UnitTestCase
{
    /**
     * @test
     */
    public function isValidShouldFailWithEmptyIdentifier()
    {
        $tag = new Tag('');
        $this->assertFalse($tag->isValid());
    }

    /**
     * @test
     */
    public function shouldGetIdentifier()
    {
        $tag = new Tag('myTag');
        $this->assertEquals('myTag', $tag->getIdentifier());
    }

    /**
     * @test
     */
    public function shouldGetIsValidWithIdentifier()
    {
        $tag = new Tag('myString');
        $this->assertEquals(true, $tag->isValid());
    }
}
