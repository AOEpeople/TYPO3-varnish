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

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;

class Http
{
    private Client $client;

    /**
     * @var PromiseInterface[]
     */
    private array $promises = [];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function request(string $method, string $url, array $headers = [], int $timeout = 0): void
    {
        $this->promises[] = $this->client->requestAsync($method, $url, [
            RequestOptions::HEADERS => $headers,
            RequestOptions::TIMEOUT => $timeout,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function wait(): array
    {
        $phrases = [];
        $results = Utils::settle($this->promises)->wait();
        foreach ($results as $result) {
            if ($result['state'] === 'fulfilled') {
                $response = $result['value'];
                if ($response instanceof Response) {
                    $phrases[] = [
                        'reason' => $response->getReasonPhrase(),
                        'success' => $response->getStatusCode() === 200,
                    ];
                }
            } elseif ($result['state'] === 'rejected') {
                $reason = $result['reason'];
                if ($reason instanceof Exception) {
                    $reason = $reason->getMessage();
                }

                $phrases[] = [
                    'reason' => $reason,
                    'success' => false,
                ];
            } else {
                $phrases[] = [
                    'reason' => 'unknown exception',
                    'success' => false,
                ];
            }
        }

        return $phrases;
    }
}
