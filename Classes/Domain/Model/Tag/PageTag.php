<?php
namespace Aoe\Varnish\Domain\Model\Tag;


use Aoe\Varnish\Domain\Model\TagInterface;

class PageTag implements TagInterface
{
    const IDS = ['pages', 'all'];

    /**
     * @var string
     */
    private $pageId;

    /**
     * @param string $pageId
     */
    public function __construct($pageId)
    {
        $this->pageId = $pageId;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->pageId;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        if (in_array($this->pageId, self::IDS)) {
            return true;
        }
        return false;
    }
}
