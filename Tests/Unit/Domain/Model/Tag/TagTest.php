<?php
namespace Aoe\Varnish\Domain\Model\Tag;

/**
 * @covers Aoe\Varnish\Domain\Model\Tag\Tag
 */
class TagTest extends \PHPUnit_Framework_TestCase
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
