<?php

namespace Aoe\Varnish\Tests\Unit\System;

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

use Aoe\Varnish\System\Http;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class HttpTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    /**
     * @var Client
     */
    private MockObject $client;

    private Http $http;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->getMockBuilder(Client::class)
            ->onlyMethods(['requestAsync'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->http = new Http($this->client);
    }

    public function testRequestShouldCallClientCorrectly(): void
    {
        $method = 'BAN';
        $url = 'domain.tld';
        $headers = ['X-Ban-Tags' => 'my_identifier'];
        $timeout = 10;

        $this->client->expects($this->once())
            ->method('requestAsync')
            ->with(
                $method,
                $url,
                [
                    RequestOptions::HEADERS => $headers,
                    RequestOptions::TIMEOUT => $timeout,
                ]
            );

        $this->http->request(
            $method,
            $url,
            $headers,
            $timeout
        );
    }

    public function testRequestShouldCallClientWithDefaultParams(): void
    {
        $method = 'BAN';
        $url = 'domain.tld';
        $headers = [];
        $timeout = 0;

        $this->client->expects($this->once())
            ->method('requestAsync')
            ->with(
                $method,
                $url,
                [
                    RequestOptions::HEADERS => $headers,
                    RequestOptions::TIMEOUT => $timeout,
                ]
            );

        $this->http->request(
            $method,
            $url
        );
    }
}
