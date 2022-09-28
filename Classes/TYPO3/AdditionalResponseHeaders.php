<?php

namespace Aoe\Varnish\TYPO3;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 AOE GmbH <dev@aoe.com>
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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class AdditionalResponseHeaders implements MiddlewareInterface
{
    private ExtensionConfiguration $extensionConfiguration;

    private Header $header;

    public function __construct(ExtensionConfiguration $extensionConfiguration, Header $header)
    {
        $this->extensionConfiguration = $extensionConfiguration;
        $this->header = $header;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $tsfe = $this->getTsfe($request);
        $this->sendPageTagHeader($tsfe);
        $this->sendDebugHeader();
        if ((int) $tsfe->page['varnish_cache'] === 1) {
            $this->header->sendEnabledHeader();
        }

        return $handler->handle($request);
    }

    private function getTsfe(ServerRequestInterface $request): TypoScriptFrontendController
    {
        // @TODO: We need the fallback '?? $GLOBALS['TSFE']' ONLY for TYPO3v10 - can be removed when we skip support for TYPO3v10!
        return $request->getAttribute('frontend.controller') ?? $GLOBALS['TSFE'];
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
        if ($this->extensionConfiguration->isDebug()) {
            $this->header->sendDebugHeader();
        }
    }
}
