<?php
namespace Aoe\Varnish\Domain\Model\Tag;

use Aoe\Varnish\Domain\Model\TagInterface;

class PageTag implements TagInterface
{
    /**
     * @return string
     */
    public function getIdentifier()
    {
        return 'typo3_page';
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return true;
    }
}
