<?php
namespace Aoe\Varnish\TYPO3\Hooks;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 AOE GmbH <dev@aoe.com>
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

use TYPO3\CMS\Backend\Toolbar\ClearCacheActionsHookInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ClearCacheMenuHook extends AbstractHook implements ClearCacheActionsHookInterface
{
    /**
     * @param array $cacheActions
     * @param array $optionValues
     */
    public function manipulateCacheActions(&$cacheActions, &$optionValues)
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $ajaxUrl = $uriBuilder->buildUriFromRoute('ajax_varnish_ban_all');

        $cacheActions[] = [
            'id' => 'varnish',
            'title' => 'LLL:EXT:varnish/Resources/Private/Language/locallang.xlf:backendAjaxHook.title',
            // Use empty description because otherwise title will also be used for it
            'description' => 'LLL:EXT:varnish/Resources/Private/Language/locallang.xlf:backendAjaxHook.description',
            'href' => $ajaxUrl,
            'iconIdentifier' => 'varnish'
        ];
    }
}
