<?php
namespace Aoe\Varnish\System;

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

use Aoe\Varnish\Domain\Model\TagInterface;
use Aoe\Varnish\TYPO3\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\SingletonInterface;

class Varnish implements SingletonInterface
{
    /**
     * @var Http
     */
    private $http;

    /**
     * @var ExtensionConfiguration
     */
    private $extensionConfiguration;

    /**
     * @var LogManager
     */
    private $logManager;

    /**
     * @param Http $http
     * @param ExtensionConfiguration $extensionConfiguration
     * @param LogManager $logManager
     */
    public function __construct(Http $http, ExtensionConfiguration $extensionConfiguration, LogManager $logManager)
    {
        $this->http = $http;
        $this->extensionConfiguration = $extensionConfiguration;
        $this->logManager = $logManager;

        register_shutdown_function([$this, 'shutdown']);
    }

    /**
     * @return array
     */
    public function shutdown()
    {
        $phrases = $this->http->wait();
        if (is_array($phrases)) {
            foreach ($phrases as $phrase) {
                if ($phrase['success']) {
                    $this->logManager->getLogger(__CLASS__)->info($phrase['reason']);
                } else {
                    $this->logManager->getLogger(__CLASS__)->alert($phrase['reason']);
                }
            }
        }
        return $phrases;
    }

    /**
     * @param TagInterface $tag
     * @return Varnish
     */
    public function banByTag(TagInterface $tag)
    {
        if (false === $tag->isValid()) {
            throw new \RuntimeException('Tag is not valid', 1435159558);
        }
        $this->request('BAN', ['X-Ban-Tags' => $tag->getIdentifier()], $this->extensionConfiguration->getBanTimeout());
        return $this;
    }

    /**
     * @return Varnish
     */
    public function banAll()
    {
        $this->request('BAN', ['X-Ban-All' => '1'], $this->extensionConfiguration->getBanTimeout());
        return $this;
    }

    /**
     * @param string $method
     * @param array $headers
     * @param integer $timeout
     */
    private function request($method, $headers = [], $timeout = null)
    {
        if ($timeout === null) {
            $timeout = $this->extensionConfiguration->getDefaultTimeout();
        }

        foreach ($this->extensionConfiguration->getHosts() as $host) {
            $this->http->request($method, $host, $headers, $timeout);
        }
    }
}
