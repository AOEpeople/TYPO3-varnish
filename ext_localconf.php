<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

/**
 * Hook to send varnish-header
 * Is used in TYPO3-Context: FE (but we should always see those hooks also in the BE-configuration-module for a better overview of configured hooks)
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'EXT:varnish/Classes/TYPO3/Hooks/ContentPostProcOutputHook.php:'.Aoe\Varnish\TYPO3\Hooks\ContentPostProcOutputHook::class.'->sendHeader';

/**
 * Hook to clear varnish-cache - hook is called when crawler-extension crawl the page
 * Is used in TYPO3-Context: FE (but we should always see those hooks also in the BE-configuration-module for a better overview of configured hooks)
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['headerNoCache']['varnish'] = Aoe\Varnish\TYPO3\Hooks\CrawlerHook::class . '->clearVarnishCache';

/**
 * Hook to clear varnish-cache - hook is called whenever the cache of an page should be deleted
 * Is used in TYPO3-Context: FE and BE
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] = 'EXT:varnish/Classes/TYPO3/Hooks/TceMainHook.php:'.Aoe\Varnish\TYPO3\Hooks\TceMainHook::class.'->clearCachePostProc';

if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
        'varnish::BAN:ALL',
        'Aoe\\Varnish\\TYPO3\\Hooks\\BackendAjaxHook->banAll'
    );

    /**
     * Hook to add 'clear-varnish-cache'-button in TYPO3-BE
     */
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['additionalBackendItems']['cacheActions'][] = 'EXT:varnish/Classes/TYPO3/Hooks/ClearCacheMenuHook.php:'.Aoe\Varnish\TYPO3\Hooks\ClearCacheMenuHook::class;
}
