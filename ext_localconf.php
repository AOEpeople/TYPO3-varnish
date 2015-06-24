<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

switch (TYPO3_MODE) {
    case 'FE':
        $TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'EXT:varnish/Classes/TYPO3/Hooks/ContentPostProcOutputHook.php:AOE\\Varnish\\TYPO3\\Hooks\\ContentPostProcOutputHook->sendHeader';
        break;
    case 'BE':
        $TYPO3_CONF_VARS['BE']['AJAX']['varnish::BAN:ALL'] = 'EXT:varnish/Classes/TYPO3/Hooks/BackendAjaxHook.php:AOE\\Varnish\\TYPO3\\Hooks\\BackendAjaxHook->banAll';
        $TYPO3_CONF_VARS['SC_OPTIONS']['additionalBackendItems']['cacheActions'][] = 'EXT:varnish/Classes/TYPO3/Hooks/ClearCacheMenuHook.php:AOE\\Varnish\\TYPO3\\Hooks\\ClearCacheMenuHook';
        $TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] = 'EXT:varnish/Classes/TYPO3/Hooks/ContentPostProcOutputHook.php:AOE\\Varnish\\TYPO3\\Hooks\\TceMainHook->clearCachePostProc';
        break;
}
