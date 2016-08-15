<?php
namespace Aoe\Varnish\System;

use Aoe\Varnish\Domain\Model\TagInterface;
use Aoe\Varnish\TYPO3\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\SingletonInterface;

class Varnish implements SingletonInterface
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
     * @var LogManager
     */
    private $logManager;

    /**
     * @param Http $http
     * @param ExtensionConfiguration $extensionConfiguration
     * @param LogManager $logManager
     */
    public function __construct(Http $http, ExtensionConfiguration $extensionConfiguration, LogManager $logManager)
    {
        $this->http = $http;
        $this->extensionConfiguration = $extensionConfiguration;
        $this->logManager = $logManager;

        register_shutdown_function([$this, 'shutdown']);
    }

    public function shutdown()
    {
        $phrases = $this->http->wait();
        if (is_array($phrases)) {
            foreach ($phrases as $phrase) {
                if ($phrase['success']) {
                    $this->logManager->getLogger(__CLASS__)->info($phrase['reason']);
                } else {
                    $this->logManager->getLogger(__CLASS__)->alert($phrase['reason']);
                }
            }
        }
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
        $this->request('BAN', ['X-Ban-Tags' => $tag->getIdentifier()]);
    }

    /**
     * @return void
     */
    public function banAll()
    {
        $this->request('BAN', ['X-Ban-All' => '1']);
    }

    /**
     * @param string $method
     * @param array $headers
     */
    private function request($method, $headers = [])
    {
        foreach ($this->extensionConfiguration->getHosts() as $host) {
            $this->http->request($method, $host, $headers);
        }
    }
}
