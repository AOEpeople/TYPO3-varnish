<?php

namespace Aoe\Varnish\TYPO3\Configuration;

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

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as Typo3ExtensionConfiguration;
use TYPO3\CMS\Core\SingletonInterface;

class ExtensionConfiguration implements SingletonInterface
{
    private array $configuration = [];

    public function __construct(Typo3ExtensionConfiguration $typo3ExtensionConfiguration)
    {
        $this->configuration = $typo3ExtensionConfiguration->get('varnish');
    }

    public function isDebug(): bool
    {
        return (bool) $this->get('debug');
    }

    /**
     * @return string[]
     */
    public function getHosts(): array
    {
        $hosts = explode(',', $this->get('hosts'));
        array_walk($hosts, static function (string &$value): void {
            if (!str_contains($value, 'https://') && !str_contains($value, 'http://')) {
                $value = 'http://' . $value;
            }
        });
        return $hosts;
    }

    public function getDefaultTimeout(): int
    {
        return (int) $this->get('default_timeout');
    }

    public function getBanTimeout(): int
    {
        return (int) $this->get('ban_timeout');
    }

    private function get(string $key): string
    {
        return $this->configuration[$key];
    }
}
