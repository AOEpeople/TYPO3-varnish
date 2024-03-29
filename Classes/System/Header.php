<?php

namespace Aoe\Varnish\System;

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

use Aoe\Varnish\Domain\Model\TagInterface;

class Header
{
    /**
     * @var string
     */
    public const HEADER_TAGS = 'X-Tags: %s';

    /**
     * @var string
     */
    public const HEADER_DEBUG = 'X-Debug: 1';

    /**
     * @var string
     */
    public const HEADER_ENABLED = 'X-Varnish-enabled: 1';

    public function sendHeaderForTag(TagInterface $tag): void
    {
        if (!$tag->isValid()) {
            throw new \RuntimeException('Tag is not valid', 1_435_047_447);
        }

        header(sprintf(self::HEADER_TAGS, $tag->getIdentifier()), false);
    }

    public function sendDebugHeader(): void
    {
        header(self::HEADER_DEBUG);
    }

    public function sendEnabledHeader(): void
    {
        header(self::HEADER_ENABLED);
    }
}
