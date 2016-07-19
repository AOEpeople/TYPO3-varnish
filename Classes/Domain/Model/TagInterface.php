<?php
namespace Aoe\Varnish\Domain\Model;

interface TagInterface
{
    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @return boolean
     */
    public function isValid();
}
