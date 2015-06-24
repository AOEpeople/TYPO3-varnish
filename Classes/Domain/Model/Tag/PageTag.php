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

use AOE\Varnish\Domain\Model\TagInterface;

/**
 * @package AOE\Varnish
 */
class PageTag implements TagInterface
{
    /**
     * @var integer
     */
    private $pageId;

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return 'page_' . $this->pageId;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        if (is_integer($this->pageId) && $this->pageId > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param integer $pageId
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }
}
