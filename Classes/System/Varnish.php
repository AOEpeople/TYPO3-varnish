<?php

namespace Aoe\Varnish\System;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 AOE GmbH <dev@aoe.com>
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

use Aoe\Varnish\Domain\Model\TagInterface;
use Aoe\Varnish\TYPO3\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\SingletonInterface;

class Varnish implements SingletonInterface
{
    public function __construct(
        private Http $http,
        private ExtensionConfiguration $extensionConfiguration,
        private LogManager $logManager
    ) {
        register_shutdown_function([$this, 'shutdown']);
    }

    /**
     * @return mixed[]
     */
    public function shutdown(): array
    {
        $phrases = $this->http->wait();
        foreach ($phrases as $phrase) {
            if ($phrase['success']) {
                $this->logManager->getLogger(self::class)
                    ->info($phrase['reason']);
            } else {
                $this->logManager->getLogger(self::class)
                    ->alert($phrase['reason']);
            }
        }

        return $phrases;
    }

    public function banByTag(TagInterface $tag): self
    {
        if (!$tag->isValid()) {
            throw new \RuntimeException('Tag is not valid', 1_435_159_558);
        }

        $this->request('BAN', ['X-Ban-Tags' => $tag->getIdentifier()], $this->extensionConfiguration->getBanTimeout());
        return $this;
    }

    public function banAll(): self
    {
        $this->request('BAN', ['X-Ban-All' => '1'], $this->extensionConfiguration->getBanTimeout());
        return $this;
    }

    public function banByRegex(string $regex): self
    {
        $this->request('BAN', ['X-Ban-Regex' => $regex], $this->extensionConfiguration->getBanTimeout());
        return $this;
    }

    private function request(string $method, array $headers = [], ?int $timeout = null): void
    {
        if ($timeout === null) {
            $timeout = $this->extensionConfiguration->getDefaultTimeout();
        }

        foreach ($this->extensionConfiguration->getHosts() as $host) {
            $this->http->request($method, $host, $headers, $timeout);
        }
    }
}
