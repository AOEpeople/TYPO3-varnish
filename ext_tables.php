<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}
$tmp = Array(
    'varnish_cache' => Array(
        'exclude' => 0,
        'label' => 'LLL:EXT:varnish/locallang_db.xml:varnish.field',
        'config' => Array(
            'type' => 'check',
            'default' => '0',
        ),
    ),
);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $tmp);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'visibility',
    'varnish_cache'
);