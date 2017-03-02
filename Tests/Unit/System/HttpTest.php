<?php
namespace Aoe\Varnish\System;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

/**
 * @covers \Aoe\Varnish\System\Http
 */
class HttpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client|\PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var Http
     */
    private $http;

    public function setUp()
    {
        $this->client = $this->getMockBuilder(Client::class)
            ->setMethods(['requestAsync'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->http = new Http($this->client);
    }

    /**
     * @test
     */
    public function requestShouldCallClientCorrectly()
    {
        $method = 'BAN';
        $url = 'domain.tld';
        $headers = ['X-Ban-Tags' => 'my_identifier'];
        $timeout = 10;

        $this->client->expects($this->once())->method('requestAsync')->with(
            $method,
            $url,
            [
                RequestOptions::HEADERS => $headers,
                RequestOptions::TIMEOUT => $timeout
            ]
        );

        $this->http->request(
            $method,
            $url,
            $headers,
            $timeout
        );
    }

    /**
     * @test
     */
    public function requestShouldCallClientWithDefaultParams()
    {
        $method = 'BAN';
        $url = 'domain.tld';
        $headers = [];
        $timeout = 0;

        $this->client->expects($this->once())->method('requestAsync')->with(
            $method,
            $url,
            [
                RequestOptions::HEADERS => $headers,
                RequestOptions::TIMEOUT => $timeout
            ]
        );

        $this->http->request(
            $method,
            $url
        );
    }
}
