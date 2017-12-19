<?php
namespace Aoe\Varnish\TYPO3\Hooks;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 AOE GmbH <dev@aoe.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

use Aoe\Varnish\Domain\Model\Tag\PageTag;
use Aoe\Varnish\Domain\Model\Tag\PageIdTag;
use Aoe\Varnish\System\Header;
use Aoe\Varnish\TYPO3\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class ContentPostProcOutputHook extends AbstractHook
{
    const TYPO3_PAGE_TAG = 'typo3_page';

    /**
     * @var Header
     */
    private $header;

    public function __construct()
    {
        parent::__construct();
        $this->header = new Header();
    }

    /**
     * @param array $parameters
     * @param TypoScriptFrontendController $parent
     */
    public function sendHeader(array $parameters, TypoScriptFrontendController $parent)
    {
        $this->sendPageTagHeader($parent);
        $this->sendDebugHeader();
        if ($parent->page['varnish_cache'] === '1') {
            $this->header->sendEnabledHeader();
        }
    }

    /**
     * @param TypoScriptFrontendController $parent
     * @return void
     */
    private function sendPageTagHeader(TypoScriptFrontendController $parent)
    {
        $pageIdTag = new PageIdTag($parent->id);
        $pageTag = new PageTag(self::TYPO3_PAGE_TAG);

        $this->header->sendHeaderForTag($pageIdTag);
        $this->header->sendHeaderForTag($pageTag);
    }

    /**
     * @return void
     */
    private function sendDebugHeader()
    {
        /** @var ExtensionConfiguration $configuration */
        $configuration = $this->objectManager->get(ExtensionConfiguration::class);
        if ($configuration->isDebug()) {
            $this->header->sendDebugHeader();
        }
    }
}
