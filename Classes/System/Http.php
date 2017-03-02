<?php
namespace Aoe\Varnish\System;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;

class Http
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var PromiseInterface[]
     */
    private $promises = [];

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $headers
     * @param integer $timeout
     */
    public function request($method, $url, $headers = [], $timeout = 0)
    {
        $this->promises[] = $this->client->requestAsync($method, $url, [
            RequestOptions::HEADERS => $headers,
            RequestOptions::TIMEOUT => $timeout
        ]);
    }

    /**
     * @return array
     * @throws \Exception|\Throwable
     */
    public function wait()
    {
        $phrases = [];
        $results = \GuzzleHttp\Promise\settle($this->promises)->wait();
        foreach ($results as $result) {
            if ($result['state'] === 'fulfilled') {
                $response = $result['value'];
                if ($response instanceof Response) {
                    $phrases[] = [
                        'reason' => $response->getReasonPhrase(),
                        'success' => $response->getStatusCode() === 200
                    ];
                }
            } else {
                if ($result['state'] === 'rejected') {
                    $reason = $result['reason'];
                    if ($reason instanceof \Exception) {
                        $reason = $reason->getMessage();
                    }
                    $phrases[] = [
                        'reason' => $reason,
                        'success' => false
                    ];
                } else {
                    $phrases[] = [
                        'reason' => 'unknown exception',
                        'success' => false
                    ];
                }
            }
        }
        return $phrases;
    }
}
