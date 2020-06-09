<?php

/**
 * Definitions for routes provided by EXT:backend
 * Contains all AJAX-based routes for entry points
 *
 * Currently the "access" property is only used so no token creation + validation is made
 * but will be extended further.
 */
return [
    'varnish_ban_all' => [
        'path' => '/varnish/ban/all',
        'target' => \Aoe\Varnish\TYPO3\Hooks\BackendAjaxHook::class . '::banAll'
    ],
];