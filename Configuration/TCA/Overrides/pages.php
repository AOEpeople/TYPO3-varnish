<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', [
    'varnish_cache' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:varnish/Resources/Private/Language/locallang_db.xlf:varnish.field',
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
