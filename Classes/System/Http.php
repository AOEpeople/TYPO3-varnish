<?php
namespace Aoe\Varnish\System;

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
