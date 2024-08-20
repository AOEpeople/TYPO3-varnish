<?php

declare(strict_types=1);

namespace Aoe\Varnish\EventListener;

use TYPO3\CMS\Backend\Backend\Event\ModifyClearCacheActionsEvent;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class ClearCacheMenu
{
    /**
     * Hook to add 'clear-varnish-cache'-button in TYPO3-BE
     */
    public function __invoke(ModifyClearCacheActionsEvent $event): void
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $cacheAction = [
            'id' => 'varnish',
            'title' => 'LLL:EXT:varnish/Resources/Private/Language/locallang.xlf:backendAjaxHook.title',
            'description' => 'LLL:EXT:varnish/Resources/Private/Language/locallang.xlf:backendAjaxHook.description',
            'href' => (string) $uriBuilder->buildUriFromRoute('ajax_varnish_ban_all'),
            'iconIdentifier' => 'varnish',
        ];

        $event->addCacheAction($cacheAction);
    }
}
