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
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class BanController extends ActionController
{
    private ModuleTemplateFactory $moduleTemplateFactory;

    private Varnish $varnish;

    public function __construct(ModuleTemplateFactory $moduleTemplateFactory, Varnish $varnish)
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
        $this->varnish = $varnish;
    }

    public function indexAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        return $moduleTemplate->renderResponse('index');
    }

    public function confirmBanTypo3PagesAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        return $moduleTemplate->renderResponse('confirmBanTypo3Pages');
    }

    public function banTypo3PagesAction(): void
    {
        $results = $this->varnish
            ->banByTag(new PageTag())
            ->shutdown();

        foreach ($results as $result) {
            if ($result['success']) {
                $this->addFlashMessage($result['reason']);
            } else {
                $this->addFlashMessage($result['reason'], '', ContextualFeedbackSeverity::ERROR);
            }
        }

        return $this->redirect('index');
    }

    public function confirmBanTagByNameAction(string $tagName): ResponseInterface
    {
        if ($this->isValidTagName($tagName)) {
            $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
            $moduleTemplate->assign('tagName', $tagName);

            return $moduleTemplate->renderResponse('confirmBanTagByName');
        }

        return $this->redirect('index');
    }

    public function banTagByNameAction(string $tagName): void
    {
        $results = $this->varnish
            ->banByTag(new Tag($tagName))
            ->shutdown();

        foreach ($results as $result) {
            if ($result['success']) {
                $this->addFlashMessage($result['reason']);
            } else {
                $this->addFlashMessage($result['reason'], '', ContextualFeedbackSeverity::ERROR);
            }
        }

        $this->redirect('index');
    }

    public function confirmBanByRegexAction(string $regex): ResponseInterface
    {
        if (!$this->isCriticalRegex($regex)) {
            $this->view->assign('regex', $regex);
            $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
            $moduleTemplate->assign('regex', $regex);

            return $moduleTemplate->renderResponse('confirmBanByRegex');
        }

        $this->redirect('index');
    }

    public function banByRegexAction(string $regex): void
    {
        $results = $this->varnish
            ->banByRegex($regex)
            ->shutdown();

        foreach ($results as $result) {
            if ($result['success']) {
                $this->addFlashMessage($result['reason']);
            } else {
                $this->addFlashMessage($result['reason'], '', ContextualFeedbackSeverity::ERROR);
            }
        }

        $this->redirect('index');
    }

    private function isCriticalRegex(string $regex): bool
    {
        if (strlen($regex) < 6) {
            $this->addFlashMessage('Bitte geben Sie einen spezifischeren RegEx ein!', '', ContextualFeedbackSeverity::ERROR);
            return true;
        }

        return false;
    }

    private function isValidTagName(string $tagName): bool
    {
        if (strlen($tagName) < 2) {
            $this->addFlashMessage('Bitte geben Sie einen gültigen Tag-Namen ein! ', '', ContextualFeedbackSeverity::ERROR);
            return false;
        }

        return true;
    }
}
