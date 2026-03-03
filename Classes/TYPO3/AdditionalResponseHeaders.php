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
use Aoe\Varnish\Domain\Model\TagInterface;
use Aoe\Varnish\TYPO3\Configuration\ExtensionConfiguration;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class AdditionalResponseHeaders implements MiddlewareInterface
{
    /**
     * @var string
     */
    public const HEADER_TAGS = 'X-Tags';

    /**
     * @var string
     */
    public const HEADER_DEBUG = 'X-Debug';

    /**
     * @var string
     */
    public const HEADER_ENABLED = 'X-Varnish-enabled';

    public function __construct(
        private ExtensionConfiguration $extensionConfiguration
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $tsfe = $this->getTsfe($request);

        if ($tsfe instanceof TypoScriptFrontendController) {
            $tags = $this->getTags($tsfe);
            foreach ($tags as $tag) {
                if ($tag->isValid()) {
                    $response = $response->withAddedHeader(self::HEADER_TAGS, $tag->getIdentifier());
                }
            }

            if ($this->extensionConfiguration->isDebug()) {
                $response = $response->withHeader(self::HEADER_DEBUG, '1');
            }

            if (isset($tsfe->page['varnish_cache']) && (int) $tsfe->page['varnish_cache'] === 1) {
                $response = $response->withHeader(self::HEADER_ENABLED, '1');
            }
        }

        return $response;
    }

    private function getTsfe(ServerRequestInterface $request): ?TypoScriptFrontendController
    {
        return $request->getAttribute('frontend.controller') ?? null;
    }

    /**
     * @return TagInterface[]
     */
    private function getTags(TypoScriptFrontendController $tsfe): array
    {
        return [
            new PageIdTag($tsfe->id),
            new PageTag(),
        ];
    }
}
