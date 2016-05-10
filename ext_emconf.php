<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Varnish',
    'description' => 'Allow varnish connection within TYPO3',
    'category' => 'misc',
    'shy' => 0,
    'version' => '0.0.1',
    'constraints' => array(
        'depends' => array(
            'php' => '5.3.0-5.5.0',
            'typo3' => '6.2.0-6.2.99',
        ),
        'conflicts' => array(
        ),
        'suggests' => array(
        ),
    ),
    'state' => 'beta',
    'author' => 'Kevin Schu',
    'author_email' => 'dev@aoe.com',
    'author_company' => 'AOE GmbH',
);