<?php
namespace Aoe\Varnish\TYPO3\Hooks;

use Aoe\Varnish\System\Varnish;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\AjaxRequestHandler;

class BackendAjaxHook extends AbstractHook
{
    /**
     * @param array $parameters
     * @param AjaxRequestHandler $parent
     */
    public function banAll(array $parameters, AjaxRequestHandler $parent)
    {
        /** @var Varnish $varnish */
        $varnish = $this->objectManager->get(Varnish::class);
        $varnish->banAll();

        if ($GLOBALS['BE_USER'] instanceof BackendUserAuthentication) {
            $GLOBALS['BE_USER']->writelog(
                3,
                1,
                0,
                0,
                'User %s has cleared the Varnish cache',
                [$GLOBALS['BE_USER']->user['username']]
            );
        }
    }
}
