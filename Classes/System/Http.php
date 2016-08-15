<?php
namespace Aoe\Varnish\System;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;

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
     */
    public function request($method, $url, $headers = [])
    {
        $this->promises[] = $this->client->requestAsync($method, $url, [
            'headers' => $headers
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
                    $phrases[] = [
                        'reason' => $result['reason'],
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
