<?php

use Aoe\Varnish\Controller\BanController;

if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'varnish',
        'web',
        'varnish',
        'bottom',
        [
            BanController::class => 'index,banTypo3Pages,confirmBanTypo3Pages,banTagByName,confirmBanTagByName,banByRegex,confirmBanByRegex'
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:varnish/Resources/Public/Icons/Extension.svg',
            'labels' => 'LLL:EXT:varnish/Resources/Private/Language/locallang_mod.xlf'
        ]
    );
}
