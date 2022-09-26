<?php
return [
    'frontend' => [
        'aoe/varnish/typo3/additionalResponseHeaders' => [
            'target' => \Aoe\Varnish\TYPO3\AdditionalResponseHeaders::class,
            'after' => [
                'typo3/cms-frontend/output-compression',
            ],
        ],
    ],
];
