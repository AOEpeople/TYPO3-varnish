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
            'Ban' => 'index,banTypo3Pages,confirmBanTypo3Pages,banTagByName,confirmBanTagByName,banByRegex,confirmBanByRegex'
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:varnish/Resources/Public/Icons/Extension.svg',
            'labels' => 'LLL:EXT:varnish/Resources/Private/Language/locallang_mod.xlf'
        ]
    );
}
