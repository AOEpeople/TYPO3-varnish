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
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class CrawlerHook extends AbstractHook
{
    /**
     * (Hook-function called from TypoScriptFrontend, see ext_localconf.php for configuration).
     *
     * @param array                        $parameters   Parameters delivered by TypoScriptFrontend
     * @param TypoScriptFrontendController $parentObject The calling parent object (TypoScriptFrontend)
     */
    public function clearVarnishCache(array $parameters, TypoScriptFrontendController $parentObject): void
    {
        if ($this->isCrawlerExtensionLoaded() && $this->isCrawlerRunning($parentObject)) {
            $this->clearPageCacheInVarnish($parentObject->id);
        }
    }

    protected function isCrawlerExtensionLoaded(): bool
    {
        return ExtensionManagementUtility::isLoaded('crawler');
    }

    private function clearPageCacheInVarnish(int $pageId): void
    {
        $pageIdTag = new PageIdTag($pageId);

        $varnish = $this->getVarnish();
        $varnish->banByTag($pageIdTag);
    }

    private function isCrawlerRunning(TypoScriptFrontendController $tsfe): bool
    {
        return isset($tsfe->applicationData['tx_crawler']['running']);
    }
}
