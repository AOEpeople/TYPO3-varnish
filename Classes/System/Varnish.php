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

namespace AOE\Varnish\System;

use AOE\Varnish\Domain\Model\TagInterface;

/**
 * @package AOE\Varnish
 */
class Varnish
{
    /**
     * @var Http
     */
    private $http;

    /**
     * @param Http $http
     */
    public function __construct(Http $http)
    {
        $this->http = $http;
    }

    /**
     * @todo replace with extension settings
     * @var array
     */
    private $hosts = array('www.congstar.local');

    /**
     * @param TagInterface $tag
     * @return void
     */
    public function banByTag(TagInterface $tag)
    {
        if(false === $tag->isValid()) {
            throw new \RuntimeException('Tag is not valid', 1435159558);
        }
        $this->call('BAN', 'X-Ban-Tags:' . $tag->getIdentifier());
    }

    /**
     * @return void
     */
    public function banAll()
    {
        $this->call('BAN', 'X-Ban-All:1');
    }

    /**
     * @param string $method
     * @param string $command
     */
    private function call($method, $command)
    {
        foreach ($this->hosts as $host) {
            $this->http->addCommand($method, $host, $command);
        }
    }
}
