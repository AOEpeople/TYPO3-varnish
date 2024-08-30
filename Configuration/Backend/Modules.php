<?php

use Aoe\Varnish\Controller\BanController;

return [
    'varnish' => [
        'parent' => 'web',
        'position' => ['after' => 'web'],
        'access' => 'user',
        'standalone' => true,
        'workspaces' => 'live',
        'iconIdentifier' => 'module-varnish',
        'labels' => 'LLL:EXT:varnish/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'Varnish',
        'controllerActions' => [
            BanController::class => [
                'index',
                'banTypo3Pages',
                'confirmBanTypo3Pages',
                'banTagByName',
                'confirmBanTagByName',
                'banByRegex',
                'confirmBanByRegex',
            ],
        ],
    ],
];