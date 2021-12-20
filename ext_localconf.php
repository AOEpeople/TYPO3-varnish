<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook to clear varnish-cache - hook is called when crawler-extension crawl the page
 * Is used in TYPO3-Context: FE (but we should always see those hooks also in the BE-configuration-module for a better overview of configured hooks)
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['headerNoCache']['varnish'] = Aoe\Varnish\TYPO3\Hooks\CrawlerHook::class . '->clearVarnishCache';

/**
 * Hook to clear varnish-cache - hook is called whenever the cache of an page should be deleted
 * Is used in TYPO3-Context: FE and BE
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] = Aoe\Varnish\TYPO3\Hooks\TceMainHook::class .'->clearCachePostProc';

if (TYPO3_MODE === 'BE') {
    /** @var IconRegistry $iconRegistry */
    $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
    $iconRegistry->registerIcon('varnish', SvgIconProvider::class, ['source' => 'EXT:varnish/Resources/Public/Icons/Extension.svg']);

    /**
     * Hook to add 'clear-varnish-cache'-button in TYPO3-BE
     */
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['additionalBackendItems']['cacheActions'][] = Aoe\Varnish\TYPO3\Hooks\ClearCacheMenuHook::class;
}
