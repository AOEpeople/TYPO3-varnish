<?php
namespace Aoe\Varnish\System;

use Aoe\Varnish\Domain\Model\TagInterface;
use Aoe\Varnish\TYPO3\Configuration\ExtensionConfiguration;

class Varnish
{
    /**
     * @var Http
     */
    private $http;

    /**
     * @var ExtensionConfiguration
     */
    private $extensionConfiguration;

    /**
     * @param Http $http
     * @param ExtensionConfiguration $extensionConfiguration
     */
    public function __construct(Http $http, ExtensionConfiguration $extensionConfiguration)
    {
        $this->http = $http;
        $this->extensionConfiguration = $extensionConfiguration;
    }

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
        foreach ($this->extensionConfiguration->getHosts() as $host) {
            $this->http->addCommand($method, $host, $command);
        }
    }
}
