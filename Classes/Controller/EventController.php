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

use AawTeam\Verowa\Domain\Model\Event;
use AawTeam\Verowa\Domain\Repository\EventRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

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
        $query = $this->eventRepository->createQuery();
        $query->setOrderings(['date_from' =>  QueryInterface::ORDER_ASCENDING]);
        $this->view->assign('events', $query->execute());
    }

    protected function detailAction(Event $event)
    {
        $this->view->assign('event', $event);
    }
}
