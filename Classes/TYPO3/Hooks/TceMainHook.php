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

namespace AOE\Varnish\TYPO3\Hooks;

use AOE\Varnish\Domain\Model\Tag\PageTag;
use AOE\Varnish\System\Varnish;

/**
 * @package AOE\Varnish
 */
class TceMainHook extends AbstractHook
{
    /**
     * @param array $parameters
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $parent
     */
    public function clearCachePostProc(array $parameters, \TYPO3\CMS\Core\DataHandling\DataHandler $parent)
    {
        /** @var Varnish $varnish */
        $varnish = $this->objectManager->get('AOE\\Varnish\\System\\Varnish');
        $pageId = isset($parameters['cacheCmd']) ? $parameters['cacheCmd'] : $parameters['uid_page'];
        if (is_numeric($pageId) && $pageId > 0) {
            $pageTag = new PageTag();
            $pageTag->setPageId($pageId);
            $varnish->banByTag($pageTag);
        }
    }
}
