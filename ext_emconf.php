<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Varnish',
    'description' => 'Allow varnish connection within TYPO3',
    'category' => 'misc',
    'shy' => 0,
    'version' => '11.0.5',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99'
        ],
        'conflicts' => [],
        'suggests' => []
    ],
    'state' => 'beta',
    'author' => 'AOE Developers',
    'author_email' => 'dev@aoe.com',
    'author_company' => 'AOE GmbH',
];
