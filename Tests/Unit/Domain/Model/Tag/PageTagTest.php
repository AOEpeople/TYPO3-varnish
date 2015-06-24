<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 AOE GmbH <dev@aoe.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace AOE\Varnish\Domain\Model\Tag;

/**
 * @package AOE\Varnish
 * @covers AOE\Varnish\Domain\Model\Tag\PageTag
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
