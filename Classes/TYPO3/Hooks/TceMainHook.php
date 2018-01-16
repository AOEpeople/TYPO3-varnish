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

use Aoe\Varnish\Domain\Model\Tag\PageTag;
use Aoe\Varnish\Domain\Model\Tag\PageIdTag;
use Aoe\Varnish\System\Varnish;
use TYPO3\CMS\Core\DataHandling\DataHandler;

class TceMainHook extends AbstractHook
{
    /**
     * @param array $parameters
     * @param DataHandler $parent
     *
     * @todo implement cache clearing for "clearCache_pageGrandParent", "clearCache_pageSiblingChildren" and
     *       and "clearCache_disable"  http://docs.typo3.org/typo3cms/TSconfigReference/PageTsconfig/TCEmain/Index.html
     * @todo find a way how to clear all affected pages after changeing a page title in the main menu or footer
     * because it is not possible through the existings hooks to access the correct page tags which needs be cleared from cache
     */
    public function clearCachePostProc(array $parameters, DataHandler $parent)
    {
        if ($this->isBackendUserInWorkspace($parent)) {
            return;
        }

        /** @var Varnish $varnish */
        $varnish = $this->objectManager->get(Varnish::class);

        // delete all Typo3 pages
        if (isset($parameters['cacheCmd']) && $parameters['cacheCmd'] === 'pages') {
            $pageTag = new PageTag('typo3_page');
            $varnish->banByTag($pageTag);
        } else {
            $pageId = $this->extractPageIdFromParameters($parameters);
            if ($pageId > 0) {
                $pageIdTag = new PageIdTag($pageId);
                $varnish->banByTag($pageIdTag);
            }
        }
    }

    /**
     * extract page id from all variants of parameters that can be given
     *
     * @param array $parameters
     * @return integer
     */
    private function extractPageIdFromParameters(array $parameters)
    {
        if (isset($parameters['table']) && $parameters['table'] === 'pages'
            && isset($parameters['uid']) && is_numeric($parameters['uid'])
        ) {
            return (integer)$parameters['uid'];
        }
        if (isset($parameters['cacheCmd']) && is_numeric($parameters['cacheCmd'])) {
            return (integer)$parameters['cacheCmd'];
        }
        if (isset($parameters['uid_page']) && is_numeric($parameters['uid_page'])) {
            return (integer)$parameters['uid_page'];
        }
        return 0;
    }

    /**
     * @param DataHandler $parent
     * @return boolean
     */
    private function isBackendUserInWorkspace(DataHandler $parent)
    {
        if ($parent->BE_USER->workspace > 0) {
            return true;
        }
        return false;
    }
}
