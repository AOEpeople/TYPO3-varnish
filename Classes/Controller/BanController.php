<?php
namespace Aoe\Varnish\Controller;

use Aoe\Varnish\Domain\Model\Tag\PageTag;
use Aoe\Varnish\Domain\Model\Tag\Tag;
use Aoe\Varnish\System\Varnish;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class BanController extends ActionController
{
    /**
     * @var Varnish
     */
    private $varnish;

    /**
     * @param Varnish $varnish
     */
    public function __construct(Varnish $varnish)
    {
        $this->varnish = $varnish;
        parent::__construct();
    }

    public function indexAction()
    {
    }

    public function banTypo3PagesAction()
    {
        $results = $this->varnish
            ->banByTag(new PageTag())
            ->shutdown();

        foreach ($results as $result) {
            if ($result['success']) {
                $this->addFlashMessage($result['reason']);
            } else {
                $this->addFlashMessage($result['reason'], '', AbstractMessage::ERROR);
            }
        }

        $this->redirect('index');
    }

    /**
     * @param string $tagName
     */
    public function banTagByNameAction($tagName)
    {
        if ($this->isValidTagName($tagName)) {
            $results = $this->varnish
                ->banByTag(new Tag($tagName))
                ->shutdown();

            foreach ($results as $result) {
                if ($result['success']) {
                    $this->addFlashMessage($result['reason']);
                } else {
                    $this->addFlashMessage($result['reason'], '', AbstractMessage::ERROR);
                }
            }
        }
        $this->redirect('index');
    }

    /**
     * @param string $tagName
     * @return bool
     */
    private function isValidTagName($tagName)
    {
        if (strlen($tagName) < 2) {
            $this->addFlashMessage('Bitte geben Sie einen gültigen Tag-Namen ein! ', '', AbstractMessage::ERROR);
            return false;
        }
        return true;
    }
}
