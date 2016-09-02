<?php
namespace Aoe\Varnish\Domain\Model\Tag;

use Aoe\Varnish\Domain\Model\TagInterface;

class Tag implements TagInterface
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        if (strlen($this->identifier) > 0) {
            return true;
        }
        return false;
    }
}
