<?php

namespace Aoe\Varnish\TYPO3\Hooks;

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
use Aoe\Varnish\Domain\Model\Tag\PageTag;
use TYPO3\CMS\Core\DataHandling\DataHandler;

class TceMainHook extends AbstractHook
{
    /**
     * @var string
     */
    private const CACHE_CMD = 'cacheCmd';

    /**
     * @var string
     */
    private const UID = 'uid';

    /**
     * @var string
     */
    private const UID_PAGE = 'uid_page';

    private static array $bannedPageIds = [];

    /**
     * @todo implement cache clearing for "clearCache_pageGrandParent", "clearCache_pageSiblingChildren" and
     *       and "clearCache_disable"  http://docs.typo3.org/typo3cms/TSconfigReference/PageTsconfig/TCEmain/Index.html
     * @todo find a way how to clear all affected pages after changeing a page title in the main menu or footer
     * because it is not possible through the existings hooks to access the correct page tags which needs be cleared from cache
     */
    public function clearCachePostProc(array $parameters, DataHandler $parent): void
    {
        if ($this->isBackendUserInWorkspace($parent)) {
            return;
        }

        $varnish = $this->getVarnish();

        if (isset($parameters[self::CACHE_CMD]) && $parameters[self::CACHE_CMD] === 'pages') {
            // ban all TYPO3-pages
            $pageTag = new PageTag();
            $varnish->banByTag($pageTag);
        } else {
            $pageId = $this->extractPageIdFromParameters($parameters);
            if ($pageId > 0 && !in_array($pageId, self::$bannedPageIds, true)) {
                // ban specific TYPO3-page
                self::$bannedPageIds[] = $pageId;
                $pageIdTag = new PageIdTag($pageId);
                $varnish->banByTag($pageIdTag);
            }
        }
    }

    /**
     * extract page id from all variants of parameters that can be given
     */
    private function extractPageIdFromParameters(array $parameters): int
    {
        if (isset($parameters['table']) && $parameters['table'] === 'pages'
            && isset($parameters[self::UID]) && is_numeric($parameters[self::UID])
        ) {
            return (int) $parameters[self::UID];
        }

        if (isset($parameters[self::CACHE_CMD]) && is_numeric($parameters[self::CACHE_CMD])) {
            return (int) $parameters[self::CACHE_CMD];
        }

        if (isset($parameters[self::UID_PAGE]) && is_numeric($parameters[self::UID_PAGE])) {
            return (int) $parameters[self::UID_PAGE];
        }

        return 0;
    }

    private function isBackendUserInWorkspace(DataHandler $parent): bool
    {
        return $parent->BE_USER->workspace > 0;
    }
}
