<?php
namespace Aoe\Varnish\TYPO3\Hooks;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 AOE GmbH <dev@aoe.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Aoe\Varnish\System\Varnish;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class BackendAjaxHook extends AbstractHook
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function banAll(ServerRequestInterface $request, ResponseInterface $response)
    {
        /** @var Varnish $varnish */
        $varnish = $this->objectManager->get(Varnish::class);
        $varnish->banAll();

        if ($this->isAuthorizedBackendSession()) {
            $this->getBackendUser()->writelog(
                3,
                1,
                0,
                0,
                'User %s has cleared the Varnish cache',
                [$this->getBackendUser()->user['username']]
            );
        }

        return json_encode($response);
    }

    /**
     * Checks if a user is logged in and the session is active.
     *
     * @return bool
     */
    protected function isAuthorizedBackendSession()
    {
        $backendUser = $this->getBackendUser();
        return $backendUser !== null && $backendUser instanceof BackendUserAuthentication && isset($backendUser->user['uid']);
    }

    /**
     * @return BackendUserAuthentication|null
     */
    protected function getBackendUser()
    {
        return isset($GLOBALS['BE_USER']) ? $GLOBALS['BE_USER'] : null;
    }
}
