<?php

declare(strict_types=1);

namespace Aoe\Varnish\EventListener;

use Aoe\Varnish\Domain\Model\Tag\PageIdTag;
use Aoe\Varnish\System\Varnish;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Event\ShouldUseCachedPageDataIfAvailableEvent;

class Crawler
{
    protected Varnish $varnish;

    /**
     * Hook to clear varnish-cache - hook is called when crawler-extension crawl the page
     * Is used in TYPO3-Context: FE (but we should always see those hooks also in the BE-configuration-module for a better overview of configured hooks)
     */
    public function __invoke(ShouldUseCachedPageDataIfAvailableEvent $event): void
    {
        $TypoScriptFrontendController = $event->getController();

        $this->clearVarnishCache($TypoScriptFrontendController);
    }

    public function clearVarnishCache(TypoScriptFrontendController $parentObject): void
    {
        if ($this->isCrawlerExtensionLoaded() && $this->isCrawlerRunning($parentObject)) {
            $this->clearPageCacheInVarnish($parentObject->id);
        }
    }

    protected function isCrawlerExtensionLoaded(): bool
    {
        return ExtensionManagementUtility::isLoaded('crawler');
    }

    protected function getVarnish(): Varnish
    {
        $this->varnish ??= GeneralUtility::makeInstance(Varnish::class);
        return $this->varnish;
    }

    private function clearPageCacheInVarnish(int $pageId): void
    {
        $pageIdTag = new PageIdTag($pageId);

        $varnish = $this->getVarnish();
        $varnish->banByTag($pageIdTag);
    }

    private function isCrawlerRunning(TypoScriptFrontendController $tsfe): bool
    {
        return isset($tsfe->applicationData['tx_crawler']['running']);
    }
}
