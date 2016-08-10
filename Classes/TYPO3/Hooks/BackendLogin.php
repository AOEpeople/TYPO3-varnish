<?php
namespace Aoe\Varnish\TYPO3\Hooks;

use Aoe\Varnish\System\Cookie;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class BackendLogin
{
    /**
     * @var string
     */
    const COOKIE_NAME = 'varnish_disable';

    /**
     * @var string
     */
    const COOKIE_VALUE = 'KLHsa89023qbvqb21pi928300';

    /**
     * @var Cookie
     */
    private $cookie;

    public function __construct()
    {
        $this->injectCookie(new Cookie());
    }

    /**
     * @param Cookie $cookie
     */
    public function injectCookie(Cookie $cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * @param array $params
     */
    public function handle(array $params)
    {
        $userAuthObject = $params['pObj'];

        if ($userAuthObject instanceof BackendUserAuthentication) {
            if ($this->hasToSetCookie($userAuthObject)) {
                $this->cookie->set(
                    self::COOKIE_NAME,
                    self::COOKIE_VALUE,
                    time() + (int)$userAuthObject->auth_timeout_field,
                    '/',
                    $this->getCookieDomain(),
                    true,
                    true
                );
            }
            if ($this->hasToUnsetCookie($userAuthObject)) {
                unset($_COOKIE[self::COOKIE_NAME]);
                $this->cookie->set(self::COOKIE_NAME, null, -1, '/');
            }
        }
    }

    /**
     * @return string
     */
    private function getCookieDomain()
    {
        $cookieDomain = $GLOBALS['TYPO3_CONF_VARS']['SYS']['cookieDomain'];
        if (!empty($GLOBALS['TYPO3_CONF_VARS']['BE']['cookieDomain'])) {
            $cookieDomain = $GLOBALS['TYPO3_CONF_VARS']['BE']['cookieDomain'];
        }
        if (!empty($GLOBALS['TYPO3_CONF_VARS']['FE']['cookieDomain'])) {
            $cookieDomain = $GLOBALS['TYPO3_CONF_VARS']['FE']['cookieDomain'];
        }
        return $cookieDomain;
    }

    /**
     * @param BackendUserAuthentication $userAuthObject
     * @return boolean
     */
    private function hasToSetCookie(BackendUserAuthentication $userAuthObject)
    {
        if ($userAuthObject->loginType === 'BE' &&
            $userAuthObject->loginFailure === false &&
            is_array($userAuthObject->user)
        ) {
            return true;
        }
        return false;
    }

    /**
     * @param BackendUserAuthentication $userAuthObject
     * @return boolean
     */
    private function hasToUnsetCookie(BackendUserAuthentication $userAuthObject)
    {
        if ($userAuthObject->loginType === 'BE' &&
            $userAuthObject->loginFailure === false &&
            !is_array($userAuthObject->user)
        ) {
            return true;
        }
        return false;
    }
}
