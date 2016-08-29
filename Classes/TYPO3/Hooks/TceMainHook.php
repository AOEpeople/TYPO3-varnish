<?php
namespace Aoe\Varnish\TYPO3\Hooks;

use Aoe\Varnish\Domain\Model\Tag\PageTag;
use Aoe\Varnish\System\Varnish;
use TYPO3\CMS\Core\DataHandling\DataHandler;

class TceMainHook extends AbstractHook
{
    /**
     * @param array $parameters
     * @param DataHandler $parent
     *
     * @todo flush cache for "cacheCmd=pages" need to flush complete varnish cache.
     * @todo implement cache clearing for "clearCache_pageGrandParent", "clearCache_pageSiblingChildren" and
     *       and "clearCache_disable"  http://docs.typo3.org/typo3cms/TSconfigReference/PageTsconfig/TCEmain/Index.html
     */
    public function clearCachePostProc(array $parameters, DataHandler $parent)
    {
        if ($this->isBackendUserInWorkspace($parent)) {
            return;
        }
        

        /** @var Varnish $varnish */
        $varnish = $this->objectManager->get(Varnish::class);
        $pageId = $this->extractPageIdFromParameters($parameters);
        if ($pageId > 0) {
            $pageTag = new PageTag($pageId);
            $varnish->banByTag($pageTag);
        }
    }

    /**
     * extract page id from all variants of parameters that can be given
     *
     * @param array $parameters
     * @return integer
     */
    private function extractPageIdFromParameters(array $parameters)
    {
        if (isset($parameters['table']) && $parameters['table'] === 'pages'
            && isset($parameters['uid']) && is_numeric($parameters['uid'])
        ) {
            return (integer)$parameters['uid'];
        }
        if (isset($parameters['cacheCmd']) && is_numeric($parameters['cacheCmd'])) {
            return (integer)$parameters['cacheCmd'];
        }
        if (isset($parameters['uid_page']) && is_numeric($parameters['uid_page'])) {
            return (integer)$parameters['uid_page'];
        }
        return 0;
    }

    /**
     * @param DataHandler $parent
     * @return boolean
     */
    private function isBackendUserInWorkspace(DataHandler $parent)
    {
        if ($parent->BE_USER->workspace > 0) {
            return true;
        }
        return false;
    }
}
