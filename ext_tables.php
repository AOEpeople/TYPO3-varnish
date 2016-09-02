<?php

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

if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Aoe.' . $_EXTKEY,
        'web',
        'varnish',
        'bottom',
        array(
            'Ban' => 'index,banTypo3Pages,banTagByName'
        ),
        array(
            'access' => '',
            'icon' => 'EXT:varnish/ext_icon.gif',
            'labels' => 'LLL:EXT:varnish/Resources/Private/Language/locallang_mod.xlf'
        )
    );
}
