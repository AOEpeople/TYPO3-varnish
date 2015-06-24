<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 AOE GmbH <dev@aoe.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace AOE\Varnish\TYPO3\Hooks;

use AOE\Varnish\Domain\Model\Tag\PageTag;
use AOE\Varnish\System\Header;
use AOE\Varnish\TYPO3\Configuration\ExtensionConfiguration;

/**
 * @package AOE\Varnish
 */
class ContentPostProcOutputHook extends AbstractHook
{
    /**
     * @var Header
     */
    private $header;

    /**
     * initialize dependencies
     */
    public function __construct()
    {
        parent::__construct();
        $this->header = new Header();
    }

    /**
     * @param array $parameters
     * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $parent
     */
    public function sendHeader(array $parameters, \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $parent)
    {
        $this->sendPageTagHeader($parent);
        $this->sendDebugHeader();
    }

    /**
     * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $parent
     * @return void
     */
    private function sendPageTagHeader(\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $parent)
    {
        $pageTag = new PageTag();
        $pageTag->setPageId($parent->id);
        $this->header->sendHeaderForTag($pageTag);
    }

    /**
     * @return void
     */
    private function sendDebugHeader()
    {
        /** @var ExtensionConfiguration $configuration */
        $configuration = $this->objectManager->get('AOE\\Varnish\\TYPO3\\Configuration\\ExtensionConfiguration');
        if ($configuration->isDebug()) {
            $this->header->sendDebugHeader();
        }
    }
}
