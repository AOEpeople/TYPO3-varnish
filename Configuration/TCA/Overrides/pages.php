<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

ExtensionManagementUtility::addTCAcolumns('pages', [
    'varnish_cache' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:varnish/Resources/Private/Language/locallang_db.xlf:varnish.field',
        'config' => [
            'type' => 'check',
            'default' => '0',
        ],
    ]
]);

ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'caching',
    'varnish_cache'
);
