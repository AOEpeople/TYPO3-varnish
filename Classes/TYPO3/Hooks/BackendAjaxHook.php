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

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\Response;

class BackendAjaxHook extends AbstractHook
{
    public function banAll(ServerRequestInterface $request): Response
    {
        $varnish = $this->getVarnish();
        $varnish->banAll();

        if ($this->isAuthorizedBackendSession()) {
            $this->getBackendUser()
                ->writelog(
                    3,
                    1,
                    0,
                    0,
                    'User %s has cleared the Varnish cache',
                    [$this->getBackendUser()->user['username']]
                );
        }

        // We need to return a response to satisfy the
        // TYPO3\CMS\Backend\Http\RouteDispatcher::dispatch()
        // don't like the solution, but haven't come up with something better yet.
        return new Response();
    }

    /**
     * Checks if a user is logged in and the session is active.
     */
    protected function isAuthorizedBackendSession(): bool
    {
        $backendUser = $this->getBackendUser();
        return $backendUser !== null && $backendUser instanceof BackendUserAuthentication && isset($backendUser->user['uid']);
    }

    protected function getBackendUser(): ?BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'] ?? null;
    }
}
