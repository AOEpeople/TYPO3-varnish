<?php
namespace Aoe\Varnish\System;

/** we replace php function "header" here in order to test the output of headers in the Hook */
function header($str, $replace = true)
{
    if ($replace) {
        $_REQUEST['headers'][] = $str;
    } else {
        foreach ($_REQUEST['headers'] as $key => $value) {
            if (strpos($value, $str) !== false) {
                $_REQUEST['headers'][$key] = $str;
                return;
            }
        }
        $_REQUEST['headers'][] = $str;
    }

}

use Aoe\Varnish\TYPO3\Hooks\ContentPostProcOutputHook;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageRepository;

class SetCorrectHeadersTest extends FunctionalTestCase
{
    /** @var string  */
    private $fixturePath = ORIGINAL_ROOT . '../../Tests/Functional/Fixtures';

    public function setUp() {

        $this->testExtensionsToLoad = [
           'typo3conf/ext/varnish'
        ];

        parent::setUp();

        try {
            $this->importDataSet($this->fixturePath . '/DataSet.xml');
        } catch (\Exception $e) {
            throw new \Error($e);
        }
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldSendPageTagHeader(){
        // setup
        $pageUid = 1;
        $this->setUpFrontendRootPage($pageUid, [ $this->fixturePath . '/RootPage.ts' ]);

        // execute
        /** @var TypoScriptFrontendController $tsfe */
        $tsfe = GeneralUtility::makeInstance(TypoScriptFrontendController::class, $GLOBALS['TYPO3_CONF_VARS'], $pageUid, 'GET');
        $tsfe->processOutput();

        // verify
        $this->assertContains(
            sprintf(Header::HEADER_TAGS, ContentPostProcOutputHook::TYPO3_PAGE_TAG),
            $_REQUEST['headers']
        );
        $this->assertContains(
            sprintf(Header::HEADER_TAGS, 'page_' . $pageUid),
            $_REQUEST['headers']
        );
    }

    /**
     * @test
     */
    public function shouldSendDebugTagHeader(){
        // setup
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['varnish'] = serialize([
            'debug' => 1
        ]);
        $pageUid = 1;
        $this->setUpFrontendRootPage($pageUid, [ $this->fixturePath . '/RootPage.ts' ]);

        // execute
        /** @var TypoScriptFrontendController $tsfe */
        $tsfe = GeneralUtility::makeInstance(TypoScriptFrontendController::class, $GLOBALS['TYPO3_CONF_VARS'], $pageUid, 'GET');
        $tsfe->processOutput();

        // verify
        $this->assertContains(
            sprintf(Header::HEADER_DEBUG, '1'),
            $_REQUEST['headers']
        );
    }

    /**
     * @test
     */
    public function shouldNotSendDebugTagHeader(){
        // setup
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['varnish'] = serialize([
            'debug' => 0
        ]);
        $pageUid = 1;
        $this->setUpFrontendRootPage($pageUid, [ $this->fixturePath . '/RootPage.ts' ]);

        // execute
        /** @var TypoScriptFrontendController $tsfe */
        $tsfe = GeneralUtility::makeInstance(TypoScriptFrontendController::class, $GLOBALS['TYPO3_CONF_VARS'], $pageUid, 'GET');
        $tsfe->processOutput();

        // verify
        $this->assertNotContains(
            sprintf(Header::HEADER_DEBUG, '1'),
            $_REQUEST['headers']
        );
        $this->assertNotContains(
            sprintf(Header::HEADER_DEBUG, '0'),
            $_REQUEST['headers']
        );
    }

    /**
     * @test
     */
    public function shouldSendVarnishEnabledTagHeader(){
        // setup
        $pageUid = 2;
        $this->setUpFrontendRootPage($pageUid, [ $this->fixturePath . '/RootPage.ts' ]);

        // execute
        /** @var TypoScriptFrontendController $tsfe */
        $tsfe = GeneralUtility::makeInstance(TypoScriptFrontendController::class, $GLOBALS['TYPO3_CONF_VARS'], $pageUid, 'GET');

        /** @var PageRepository sys_page */
        $tsfe->sys_page = GeneralUtility::makeInstance(PageRepository::class);
        $tsfe->getPageAndRootline();

        $tsfe->processOutput();

        // verify
        $this->assertContains(
            sprintf(Header::HEADER_ENABLED, '1'),
            $_REQUEST['headers']
        );
    }

    /**
     * @test
     */
    public function shouldNotSendVarnishEnabledTagHeader(){
        // setup
        $pageUid = 3;
        $this->setUpFrontendRootPage($pageUid, [ $this->fixturePath . '/RootPage.ts' ]);

        // execute
        /** @var TypoScriptFrontendController $tsfe */
        $tsfe = GeneralUtility::makeInstance(TypoScriptFrontendController::class, $GLOBALS['TYPO3_CONF_VARS'], $pageUid, 'GET');

        /** @var PageRepository sys_page */
        $tsfe->sys_page = GeneralUtility::makeInstance(PageRepository::class);
        $tsfe->getPageAndRootline();

        $tsfe->processOutput();

        // verify
        $this->assertNotContains(
            sprintf(Header::HEADER_ENABLED, '1'),
            $_REQUEST['headers']
        );
        $this->assertNotContains(
            sprintf(Header::HEADER_ENABLED, '0'),
            $_REQUEST['headers']
        );
    }
}