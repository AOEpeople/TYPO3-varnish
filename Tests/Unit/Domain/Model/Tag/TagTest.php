<?php
namespace Aoe\Varnish\Domain\Model\Tag;

/**
 * @covers Aoe\Varnish\Domain\Model\Tag\Tag
 */
class TagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Tag
     */
    private $tag;

    public function setUp()
    {
        $this->tag = new Tag();
    }

    /**
     * @test
     */
    public function isValidShouldFailWithEmptyIdentifier()
    {
        $this->assertFalse($this->tag->isValid());
    }

    /**
     * @test
     */
    public function shouldGetIdentifier()
    {
        $this->tag->setIdentifier('myTag');
        $this->assertEquals('myTag', $this->tag->getIdentifier());
    }

    public function shouldGetIsValidWithIdentifier()
    {
        $this->tag->setIdentifier('myString');
        $this->assertEquals(true, $this->tag->isValid());
    }
}
