<?php
namespace Aoe\Varnish\TYPO3\Hooks;

use TYPO3\CMS\Backend\Toolbar\ClearCacheActionsHookInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Lang\LanguageService;

class ClearCacheMenuHook extends AbstractHook implements ClearCacheActionsHookInterface
{
    /**
     * @param array $cacheActions
     * @param array $optionValues
     */
    public function manipulateCacheActions(&$cacheActions, &$optionValues)
    {
        /** @var LanguageService $languageService */
        $languageService = $this->objectManager->get(LanguageService::class);
        $title = $languageService->sL('LLL:EXT:varnish/Resources/Private/Language/locallang.xlf:backendAjaxHook.title');

        $cacheActions[] = array(
            'id' => 'varnish',
            'title' => $title,
            //@todo change naming of ajax call "BAN:ALL"
            'href' =>  BackendUtility::getAjaxUrl('varnish::BAN:ALL', []),
            'icon' => '<img src="/' . $GLOBALS['TYPO3_LOADED_EXT']['varnish']['siteRelPath'] .
                'ext_icon.gif" title="' . $title . '" alt="' . $title . '" />',
        );
    }
}
