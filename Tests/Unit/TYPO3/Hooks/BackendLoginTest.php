<?php
namespace Aoe\Varnish\TYPO3\Hooks;

use Aoe\Varnish\System\Cookie;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * @covers Aoe\Varnish\TYPO3\Hooks\BackendLogin
 */
class BackendLoginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    public static $cookies = [];

    /**
     * @test
     */
    public function shouldSetNoCookieInvalidAuthenticationClass()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|FrontendUserAuthentication $userAuthObject */
        $userAuthObject = $this->getMockBuilder(FrontendUserAuthentication::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userAuthObject->loginType = 'FE';
        $userAuthObject->loginFailure = false;

        $backendLogin = new BackendLogin();

        /** @var \PHPUnit_Framework_MockObject_MockObject|Cookie $cookie */
        $cookie = $this->getMockBuilder(Cookie::class)
            ->setMethods(array('set'))
            ->getMock();
        $cookie->expects($this->never())->method('set');

        $backendLogin->injectCookie($cookie);

        $backendLogin->handle(['pObj' => $userAuthObject]);
    }

    /**
     * @test
     */
    public function shouldSetNoCookieForFrontend()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|BackendUserAuthentication $userAuthObject */
        $userAuthObject = $this->getMockBuilder(BackendUserAuthentication::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userAuthObject->loginType = 'FE';
        $userAuthObject->loginFailure = false;

        $backendLogin = new BackendLogin();

        /** @var \PHPUnit_Framework_MockObject_MockObject|Cookie $cookie */
        $cookie = $this->getMockBuilder(Cookie::class)
            ->setMethods(array('set'))
            ->getMock();
        $cookie->expects($this->never())->method('set');

        $backendLogin->injectCookie($cookie);

        $backendLogin->handle(['pObj' => $userAuthObject]);
    }

    /**
     * @test
     */
    public function shouldUnsetCookie()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|BackendUserAuthentication $userAuthObject */
        $userAuthObject = $this->getMockBuilder(BackendUserAuthentication::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userAuthObject->loginType = 'BE';
        $userAuthObject->loginFailure = false;
        $userAuthObject->user = null;

        $backendLogin = new BackendLogin();

        /** @var \PHPUnit_Framework_MockObject_MockObject|Cookie $cookie */
        $cookie = $this->getMockBuilder(Cookie::class)
            ->setMethods(array('set'))
            ->getMock();
        $cookie->expects($this->once())->method('set')->with('varnish_disable', null, -1, '/');

        $backendLogin->injectCookie($cookie);

        $backendLogin->handle(['pObj' => $userAuthObject]);
    }

    /**
     * @test
     */
    public function shouldSetCookie()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|BackendUserAuthentication $userAuthObject */
        $userAuthObject = $this->getMockBuilder(BackendUserAuthentication::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userAuthObject->loginType = 'BE';
        $userAuthObject->loginFailure = false;
        $userAuthObject->user = ['uid' => 4711];

        $backendLogin = new BackendLogin();

        /** @var \PHPUnit_Framework_MockObject_MockObject|Cookie $cookie */
        $cookie = $this->getMockBuilder(Cookie::class)
            ->setMethods(array('set'))
            ->getMock();
        $cookie->expects($this->once())->method('set')->with('varnish_disable', 'KLHsa89023qbvqb21pi928300');

        $backendLogin->injectCookie($cookie);

        $backendLogin->handle(['pObj' => $userAuthObject]);
    }
}
