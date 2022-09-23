<?php

namespace Aoe\Varnish\TYPO3\Hooks;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 AOE GmbH <dev@aoe.com>
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

use Aoe\Varnish\Domain\Model\Tag\PageIdTag;
use Aoe\Varnish\Domain\Model\Tag\PageTag;
use Aoe\Varnish\System\Header;
use Aoe\Varnish\TYPO3\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class ContentPostProcOutputHook extends AbstractHook
{
    private Header $header;

    public function __construct()
    {
        $this->header = new Header();
    }

    public function sendHeader(array $parameters, TypoScriptFrontendController $parent): void
    {
        $this->sendPageTagHeader($parent);
        $this->sendDebugHeader();
        if ((int) $parent->page['varnish_cache'] === 1) {
            $this->header->sendEnabledHeader();
        }
    }

    private function sendPageTagHeader(TypoScriptFrontendController $parent): void
    {
        $pageIdTag = new PageIdTag($parent->id);
        $pageTag = new PageTag();

        $this->header->sendHeaderForTag($pageIdTag);
        $this->header->sendHeaderForTag($pageTag);
    }

    private function sendDebugHeader(): void
    {
        $configuration = new ExtensionConfiguration();
        if ($configuration->isDebug()) {
            $this->header->sendDebugHeader();
        }
    }
}
