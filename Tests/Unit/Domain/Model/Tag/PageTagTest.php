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

use Aoe\Varnish\Domain\Model\Tag\PageTag;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class PageTagTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    public function testTagShouldBeValid(): void
    {
        $tag = new PageTag();
        $this->assertTrue($tag->isValid());
    }

    public function testShouldGetIdentifier(): void
    {
        $tag = new PageTag();
        $this->assertSame('typo3_page', $tag->getIdentifier());
    }
}
