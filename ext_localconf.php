<?php
defined('TYPO3') or die();

/**
 * Hook to clear varnish-cache - hook is called whenever the cache of a page should be deleted
 * Is used in TYPO3-Context: FE and BE
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] = Aoe\Varnish\TYPO3\Hooks\TceMainHook::class .'->clearCachePostProc';
