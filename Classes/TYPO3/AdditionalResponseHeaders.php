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
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class AdditionalResponseHeaders implements MiddlewareInterface
{
    /**
     * @var ExtensionConfiguration
     */
    private $extensionConfiguration;

    /**
     * @var Header
     */
    private $header;

    /**
     * @param ExtensionConfiguration $extensionConfiguration
     * @param Header $header
     */
    public function __construct(ExtensionConfiguration $extensionConfiguration, Header $header)
    {
        $this->extensionConfiguration = $extensionConfiguration;
        $this->header = $header;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $tsfe = $this->getTsfe($request);
        $this->sendPageTagHeader($tsfe);
        $this->sendDebugHeader();
        if ((int)$tsfe->page['varnish_cache'] === 1) {
            $this->header->sendEnabledHeader();
        }

        return $handler->handle($request);
    }

    /**
     * @param ServerRequest $request
     * @return TypoScriptFrontendController
     */
    private function getTsfe(ServerRequestInterface $request)
    {
        return $request->getAttribute('frontend.controller');
    }

    /**
     * @param TypoScriptFrontendController $parent
     * @return void
     */
    private function sendPageTagHeader(TypoScriptFrontendController $parent)
    {
        $pageIdTag = new PageIdTag($parent->id);
        $pageTag = new PageTag();

        $this->header->sendHeaderForTag($pageIdTag);
        $this->header->sendHeaderForTag($pageTag);
    }

    /**
     * @return void
     */
    private function sendDebugHeader()
    {
        if ($this->extensionConfiguration->isDebug()) {
            $this->header->sendDebugHeader();
        }
    }
}
