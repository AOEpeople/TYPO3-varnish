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

/**
 * @package AOE\Varnish
 */
class Http
{
    /**
     * @var resource
     */
    private $curlQueue;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        if (!extension_loaded('curl')) {
            throw new \Exception('The cURL PHP extension is required.', 1434980440);
        }
        self::initQueue();
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        self::runQueue();
    }

    /**
     * @param string $method
     * @param string $url
     * @param string $header
     */
    public function addCommand($method, $url, $header = '')
    {
        if (!is_array($header)) {
            $header = array($header);
        }

        $curlHandle = curl_init();
        $curlOptions = array(
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_TIMEOUT => 1,
            CURLOPT_RETURNTRANSFER => 1,
        );

        curl_setopt_array($curlHandle, $curlOptions);
        curl_multi_add_handle($this->curlQueue, $curlHandle);
    }

    /**
     * @return void
     */
    private function initQueue()
    {
        $this->curlQueue = curl_multi_init();
    }

    /**
     * @return void
     */
    private function runQueue()
    {
        $running = null;
        do {
            $code = curl_multi_exec($this->curlQueue, $running);
        } while ($running);
        curl_multi_close($this->curlQueue);
    }
}
