<?php
namespace Aoe\Varnish\TYPO3\Hooks;

use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

abstract class AbstractHook
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     */
    protected $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function __construct()
    {
        $this->objectManager = new ObjectManager();
    }
}
