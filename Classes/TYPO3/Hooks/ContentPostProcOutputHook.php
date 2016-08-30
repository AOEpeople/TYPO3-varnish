<?php
namespace Aoe\Varnish\TYPO3\Hooks;

use Aoe\Varnish\Domain\Model\Tag\PageTag;
use Aoe\Varnish\System\Header;
use Aoe\Varnish\TYPO3\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class ContentPostProcOutputHook extends AbstractHook
{
    /**
     * @var Header
     */
    private $header;

    public function __construct()
    {
        parent::__construct();
        $this->header = new Header();
    }

    /**
     * @param array $parameters
     * @param TypoScriptFrontendController $parent
     */
    public function sendHeader(array $parameters, TypoScriptFrontendController $parent)
    {
        $this->sendPageTagHeader($parent);
        $this->sendDebugHeader();
        if ($parent->page['varnish_cache'] === '1') {
            $this->header->sendEnabledHeader();
        }
    }

    /**
     * @param TypoScriptFrontendController $parent
     * @return void
     */
    private function sendPageTagHeader(TypoScriptFrontendController $parent)
    {
        $pageTag = new PageTag($parent->id);
        $this->header->sendHeaderForTag($pageTag);
    }

    /**
     * @return void
     */
    private function sendDebugHeader()
    {
        /** @var ExtensionConfiguration $configuration */
        $configuration = $this->objectManager->get(ExtensionConfiguration::class);
        if ($configuration->isDebug()) {
            $this->header->sendDebugHeader();
        }
    }
}
