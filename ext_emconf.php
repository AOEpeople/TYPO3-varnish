<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Varnish',
    'description' => 'Allow varnish connection within TYPO3',
    'category' => 'misc',
    'shy' => 0,
    'version' => '1.4.0',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-8.7.99'
        ],
        'conflicts' => [],
        'suggests' => []
    ],
    'state' => 'beta',
    'author' => 'Kevin Schu',
    'author_email' => 'dev@aoe.com',
    'author_company' => 'AOE GmbH',
];
