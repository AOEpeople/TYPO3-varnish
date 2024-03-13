<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Varnish',
    'description' => 'Allow varnish connection within TYPO3',
    'category' => 'misc',
    'version' => '11.1.0',
    'shy' => '',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'state' => 'beta',
    'clearCacheOnLoad' => 0,
    'author' => 'AOE Developers',
    'author_email' => 'dev@aoe.com',
    'author_company' => 'AOE GmbH',
];
