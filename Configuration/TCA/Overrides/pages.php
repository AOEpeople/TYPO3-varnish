<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', [
    'varnish_cache' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:varnish/locallang_db.xml:varnish.field',
        'config' => [
            'type' => 'check',
            'default' => '0',
        ],
    ]
]);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'caching',
    'varnish_cache'
);
