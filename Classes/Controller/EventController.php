<?php
declare(strict_types=1);
namespace AawTeam\Verowa\Controller;
/*
 * Copyright by Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use AawTeam\Verowa\Domain\Repository\EventRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * EventController
 */
class EventController extends ActionController
{
    /**
     * @var EventRepository
     */
    protected $eventRepository;

    /**
     * @param EventRepository $eventRepository
     */
    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    protected function listAction()
    {
        $this->view->assign('events', $this->eventRepository->findAll());
    }
}
