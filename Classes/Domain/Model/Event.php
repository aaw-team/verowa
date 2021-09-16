<?php
declare(strict_types=1);
namespace AawTeam\Verowa\Domain\Model;
/*
 * Copyright by Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Event
 */
class Event extends AbstractEntity
{
    protected $eventId;
    protected $dateFrom;
    protected $dateTo;
    protected $hideTime;
    protected $dateText;
    protected $title;
    protected $topic;
    protected $shortDesc;
    protected $longDesc;
    protected $subscribeDate;
    protected $imageUrl;

    /**
     * @var ObjectStorage<File>
     */
    protected $files;

    /**
     * @var ObjectStorage<Room>
     */
    protected $rooms;

    public function initializeObject()
    {
        $this->rooms = new ObjectStorage();
        $this->files = new ObjectStorage();
    }

    public function getEventId(): int
    {
        return $this->eventId;
    }

    public function setEventId(int $eventId)
    {
        $this->eventId = $eventId;
    }

    public function getDateFrom(): \DateTime
    {
        return $this->dateFrom;
    }

    public function setDateFrom(\DateTime $dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

    public function getDateTo(): \DateTime
    {
        return $this->dateTo;
    }

    public function setDateTo(\DateTime $dateTo)
    {
        $this->dateTo = $dateTo;
    }

    public function getHideTime(): bool
    {
        return $this->hideTime;
    }

    public function setHideTime(bool $hideTime)
    {
        $this->hideTime = $hideTime;
    }

    public function getDateText(): string
    {
        return $this->dateText;
    }

    public function setDateText(string $dateText)
    {
        $this->dateText = $dateText;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function setTopic(string $topic)
    {
        $this->topic = $topic;
    }

    public function getShortDesc(): string
    {
        return $this->shortDesc;
    }

    public function setShortDesc(string $shortDesc)
    {
        $this->shortDesc = $shortDesc;
    }

    public function getLongDesc(): string
    {
        return $this->longDesc;
    }

    public function setLongDesc(string $longDesc)
    {
        $this->longDesc = $longDesc;
    }

    public function getSubscribeDate(): ?\DateTime
    {
        return $this->subscribeDate;
    }

    public function setSubscribeDate(?\DateTime $subscribeDate)
    {
        $this->subscribeDate = $subscribeDate;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    public function getRooms(): ObjectStorage
    {
        return $this->rooms;
    }

    public function setRooms(ObjectStorage $rooms)
    {
        $this->rooms = $rooms;
    }

    public function getFiles(): ObjectStorage
    {
        return $this->files;
    }

    public function setFiles(ObjectStorage $files)
    {
        $this->files = $files;
    }
}
