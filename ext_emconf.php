<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3 Varnish',
    'description' => 'Allow varnish connection within TYPO3',
    'category' => 'misc',
    'version' => '11.1.1',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'state' => 'beta',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author' => 'AOE Developers',
    'author_email' => 'dev@aoe.com',
    'author_company' => 'AOE GmbH',
];
