<?php
namespace Aoe\Varnish\System;

use Aoe\Varnish\Domain\Model\TagInterface;

class Header
{
    /**
     * @var string
     */
    const HEADER_TAGS = 'X-Tags: %s';

    /**
     * @var string
     */
    const HEADER_DEBUG = 'X-Debug: 1';

    /**
     * @var string
     */
    const HEADER_ENABLED = 'X-Varnish-enabled: 1';

    /**
     * @param TagInterface $tag
     * @return void
     */
    public function sendHeaderForTag(TagInterface $tag)
    {
        if (false === $tag->isValid()) {
            throw new \RuntimeException('Tag is not valid', 1435047447);
        }
        header(sprintf(self::HEADER_TAGS, $tag->getIdentifier()), false);
    }

    /**
     * @return void
     */
    public function sendDebugHeader()
    {
        header(self::HEADER_DEBUG);
    }

    /**
     * @return void
     */
    public function sendEnabledHeader()
    {
        header(self::HEADER_ENABLED);
    }
}
