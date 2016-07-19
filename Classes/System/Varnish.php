<?php
namespace Aoe\Varnish\System;

use Aoe\Varnish\Domain\Model\TagInterface;

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
        if (false === $tag->isValid()) {
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
