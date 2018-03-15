<?php
namespace Aoe\Varnish\Controller;

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
     * @param string $regex
     */
    public function banByRegexAction($regex)
    {
        if (!$this->isCriticalRegex($regex)) {
            $this->view->assign('regex', $regex);
        } else {
            $this->redirect('index');
        }
    }

    /**
     * @param string $regex
     */
    public function confirmBanByRegexAction($regex)
    {
        if (!$this->isCriticalRegex($regex)){
            $results = $this->varnish
                ->banByRegex($regex)
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
     * @param string $regex
     * @return bool
     */
    private function isCriticalRegex($regex)
    {
        if (strlen($regex) < 3) {
            $this->addFlashMessage('Bitte geben Sie einen spezifischeren RegEx ein!', '', AbstractMessage::ERROR);
            return true;
        }
        return false;
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
