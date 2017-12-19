<?php
namespace Aoe\Varnish\TYPO3\Configuration;

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

use TYPO3\CMS\Core\SingletonInterface;

class ExtensionConfiguration implements SingletonInterface
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * ExtensionConfiguration constructor.
     */
    public function __construct()
    {
        $this->configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['varnish']);
    }

    /**
     * @return boolean
     */
    public function isDebug()
    {
        return (boolean)$this->get('debug');
    }

    /**
     * @return array
     */
    public function getHosts()
    {
        $hosts = explode(',', $this->get('hosts'));
        array_walk($hosts, function (&$value) {
            if (false === strpos($value, 'https://') && false === strpos($value, 'http://')) {
                $value = 'http://' . $value;
            }
        });
        return $hosts;
    }

    /**
     * @return integer
     */
    public function getDefaultTimeout()
    {
        return (int)$this->get('default_timeout');
    }

    /**
     * @return integer
     */
    public function getBanTimeout()
    {
        return (int)$this->get('ban_timeout');
    }

    /**
     * @param string $key
     * @return string
     */
    private function get($key)
    {
        return $this->configuration[$key];
    }
}
