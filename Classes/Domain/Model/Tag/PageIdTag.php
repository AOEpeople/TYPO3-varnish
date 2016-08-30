<?php
namespace Aoe\Varnish\Domain\Model\Tag;

use Aoe\Varnish\Domain\Model\TagInterface;

class PageIdTag implements TagInterface
{
    /**
     * @var integer
     */
    private $pageId;

    /**
     * @param integer $pageId
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
        return 'page_' . $this->pageId;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        if (is_integer($this->pageId) && $this->pageId > 0) {
            return true;
        }
        return false;
    }
}
