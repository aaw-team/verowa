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

/**
 * Room
 */
class Room extends AbstractEntity
{
    protected $roomId;
    protected $roomName;
    protected $shortcut;
    protected $locationId;
    protected $locationName;
    protected $street;
    protected $postcode;
    protected $city;
    protected $locationUrl;
    protected $locationUrlIsExternal;

    public function getRoomId(): int
    {
        return $this->roomId;
    }

    public function setRoomId(int $roomId)
    {
        $this->roomId = $roomId;
    }

    public function getRoomName(): string
    {
        return $this->roomName;
    }

    public function setRoomName(string $roomName)
    {
        $this->roomName = $roomName;
    }

    public function getShortcut(): string
    {
        return $this->shortcut;
    }

    public function setShortcut(string $shortcut)
    {
        $this->shortcut = $shortcut;
    }

    public function getLocationId(): int
    {
        return $this->locationId;
    }

    public function setLocationId(int $locationId)
    {
        $this->locationId = $locationId;
    }

    public function getLocationName(): string
    {
        return $this->locationName;
    }

    public function setLocationName(string $locationName)
    {
        $this->locationName = $locationName;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street)
    {
        $this->street = $street;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode)
    {
        $this->postcode = $postcode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city)
    {
        $this->city = $city;
    }

    public function getLocationUrl(): string
    {
        return $this->locationUrl;
    }

    public function setLocationUrl(string $locationUrl)
    {
        $this->locationUrl = $locationUrl;
    }

    public function getLocationUrlIsExternal(): bool
    {
        return $this->locationUrlIsExternal;
    }

    public function setLocationUrlIsExternal(bool $locationUrlIsExternal)
    {
        $this->locationUrlIsExternal = $locationUrlIsExternal;
    }
}
