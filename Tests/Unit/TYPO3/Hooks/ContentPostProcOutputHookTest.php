<?php
namespace Aoe\Varnish\TYPO3\Hooks;

use Aoe\Varnish\System\Header;
use Aoe\Varnish\TYPO3\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * @covers \Aoe\Varnish\TYPO3\Hooks\ContentPostProcOutputHook
 */
class ContentPostProcOutputHookTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentPostProcOutputHook
     */
    private $hook;

    /**
     * @var TypoScriptFrontendController
     */
    private $frontendController;

    /**
     * @test
     */
    public function shouldSendAllHeader()
    {
        // mocking
        $header = $this->getHeaderMock(1, 2, 1);

        $this->frontendController = $this
            ->getMockBuilder(TypoScriptFrontendController::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->setTypoScriptFrontendControllerReflectionProperties(
            $this->frontendController,
            12345,
            '1'
        );

        $extensionConfiguration = $this->getExtensionConfigurationMock(true);
        $objectManager = $this->getObjectManagerMock($extensionConfiguration);

        $this->hook = new ContentPostProcOutputHook();

        $hookReflection = new \ReflectionClass($this->hook);
        $reflectionPropertyHeader = $hookReflection->getProperty('header');
        $reflectionPropertyHeader->setAccessible(true);
        $reflectionPropertyHeader->setValue($this->hook, $header);

        /** @var ObjectManagerInterface $objectManager */
        $this->hook->injectObjectManager($objectManager);

        // execute
        $this->hook->sendHeader([], $this->frontendController);
    }

    /**
     * @test
     */
    public function shouldNotSendDebugHeader()
    {
        // mocking
        $header = $this->getHeaderMock(1, 2, 0);

        $this->frontendController = $this
            ->getMockBuilder(TypoScriptFrontendController::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->setTypoScriptFrontendControllerReflectionProperties(
            $this->frontendController,
            12345,
            '1'
        );

        $extensionConfiguration = $this->getExtensionConfigurationMock(false);
        $objectManager = $this->getObjectManagerMock($extensionConfiguration);

        $this->hook = new ContentPostProcOutputHook();

        $hookReflection = new \ReflectionClass($this->hook);
        $reflectionPropertyHeader = $hookReflection->getProperty('header');
        $reflectionPropertyHeader->setAccessible(true);
        $reflectionPropertyHeader->setValue($this->hook, $header);

        /** @var ObjectManagerInterface $objectManager */
        $this->hook->injectObjectManager($objectManager);

        // execute
        $this->hook->sendHeader([], $this->frontendController);
    }

    /**
     * @test
     */
    public function shouldNotSendVarnishEnabledHeader()
    {
        // mocking
        $header = $this->getHeaderMock(0, 2, 1);

        $this->frontendController = $this
            ->getMockBuilder(TypoScriptFrontendController::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->setTypoScriptFrontendControllerReflectionProperties(
            $this->frontendController,
            12345,
            '0'
        );

        $extensionConfiguration = $this->getExtensionConfigurationMock(true);
        $objectManager = $this->getObjectManagerMock($extensionConfiguration);

        $this->hook = new ContentPostProcOutputHook();

        $hookReflection = new \ReflectionClass($this->hook);
        $reflectionPropertyHeader = $hookReflection->getProperty('header');
        $reflectionPropertyHeader->setAccessible(true);
        $reflectionPropertyHeader->setValue($this->hook, $header);

        /** @var ObjectManagerInterface $objectManager */
        $this->hook->injectObjectManager($objectManager);

        // execute
        $this->hook->sendHeader([], $this->frontendController);
    }

    /**
     * @param int $sendEnabledHeaderCallingCount
     * @param int $sendHeaderForTagCallingCount
     * @param int $sendDebugHeaderCallingCount
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getHeaderMock(
        $sendEnabledHeaderCallingCount,
        $sendHeaderForTagCallingCount,
        $sendDebugHeaderCallingCount
    ) {
        $header = $this->getMockBuilder(Header::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendEnabledHeader', 'sendHeaderForTag', 'sendDebugHeader'])
            ->getMock();

        $header->expects($this->exactly($sendEnabledHeaderCallingCount))
            ->method('sendEnabledHeader');

        $header->expects($this->exactly($sendHeaderForTagCallingCount))
            ->method('sendHeaderForTag');

        $header->expects($this->exactly($sendDebugHeaderCallingCount))
            ->method('sendDebugHeader');

        return $header;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $extensionConfiguration
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getObjectManagerMock(\PHPUnit_Framework_MockObject_MockObject $extensionConfiguration)
    {
        $objectManager = $this->getMockBuilder(ObjectManagerInterface::class)
            ->setMethods(array('isRegistered', 'get', 'create', 'getEmptyObject', 'getScope'))
            ->getMock();

        $objectManager->expects($this->any())
            ->method('get')
            ->with(ExtensionConfiguration::class)
            ->willReturn($extensionConfiguration);

        return $objectManager;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $object
     * @param int $pageId
     * @param string $varnishCacheEnabled
     */
    private function setTypoScriptFrontendControllerReflectionProperties(
        \PHPUnit_Framework_MockObject_MockObject $object,
        $pageId,
        $varnishCacheEnabled
    ) {
        $reflection = new \ReflectionClass($object);
        $reflectionPropertyId = $reflection->getProperty('id');
        $reflectionPropertyId->setAccessible(true);
        $reflectionPropertyId->setValue($this->frontendController, $pageId);

        $reflectionPropertyId = $reflection->getProperty('page');
        $reflectionPropertyId->setAccessible(true);
        $reflectionPropertyId->setValue(
            $this->frontendController,
            ['varnish_cache' => $varnishCacheEnabled]
        );
    }

    /**
     * @param boolean $debugEnabled
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getExtensionConfigurationMock($debugEnabled)
    {
        $extensionConfiguration = $this
            ->getMockBuilder(ExtensionConfiguration::class)
            ->disableOriginalConstructor()
            ->setMethods(['isDebug'])
            ->getMock();

        $extensionConfiguration->expects($this->once())
            ->method('isDebug')
            ->willReturn($debugEnabled);

        return $extensionConfiguration;
    }
}
