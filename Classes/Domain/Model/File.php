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
 * File
 */
class File extends AbstractEntity
{
    protected $event;
    protected $fileName;
    protected $desc;
    protected $url;
    protected $filesizeKb;
    protected $fileType;

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public function getDesc(): string
    {
        return $this->desc;
    }

    public function setDesc(string $desc)
    {
        $this->desc = $desc;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    public function getFilesizeKb(): int
    {
        return $this->filesizeKb;
    }

    public function setFilesizeKb(int $filesizeKb)
    {
        $this->filesizeKb = $filesizeKb;
    }

    public function getFileType(): string
    {
        return $this->fileType;
    }

    public function setFileType(string $fileType)
    {
        $this->fileType = $fileType;
    }
}
