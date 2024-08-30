<?php

$EM_CONF['varnish'] = [
    'title' => 'TYPO3 Varnish',
    'description' => 'Allow varnish connection within TYPO3',
    'category' => 'misc',
    'version' => '12.0.2',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
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
