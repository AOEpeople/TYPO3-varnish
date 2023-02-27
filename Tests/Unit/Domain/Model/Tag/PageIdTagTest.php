<?php

namespace Aoe\Varnish\Tests\Unit\Domain\Model\Tag;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 AOE GmbH <dev@aoe.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

use Aoe\Varnish\Domain\Model\Tag\PageIdTag;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * @covers \Aoe\Varnish\Domain\Model\Tag\PageTag
 */
class PageIdTagTest extends UnitTestCase
{
    public function testIsValidShouldFailWithNegativePageId()
    {
        $tag = new PageIdTag(-1);
        self::assertFalse($tag->isValid());
    }

    public function testIsValidShouldFailWithPageIdZero()
    {
        $tag = new PageIdTag(0);
        self::assertFalse($tag->isValid());
    }

    public function testIsValidShouldFailWithPageIdNotNumeric()
    {
        $tag = new PageIdTag('string');
        self::assertFalse($tag->isValid());
    }

    public function testIsValidWithInteger()
    {
        $tag = new PageIdTag(11);
        self::assertTrue($tag->isValid());
    }

    public function testGetIdentifier()
    {
        $tag = new PageIdTag(11);
        self::assertSame('page_11', $tag->getIdentifier());
    }
}
