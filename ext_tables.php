<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Aoe.varnish',
        'web',
        'varnish',
        'bottom',
        [
            'Ban' => 'index,banTypo3Pages,banTagByName,banByRegex,confirmBanByRegex'
        ],
        [
            'access' => '',
            'icon' => 'EXT:varnish/ext_icon.svg',
            'labels' => 'LLL:EXT:varnish/Resources/Private/Language/locallang_mod.xlf'
        ]
    );
}
