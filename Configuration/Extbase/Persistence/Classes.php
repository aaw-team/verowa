<?php
declare(strict_types = 1);
/*
 * Copyright by Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

return [
    \AawTeam\Verowa\Domain\Model\Event::class => [
        'tableName' => 'tx_verowa_event',
    ],
    \AawTeam\Verowa\Domain\Model\Room::class => [
        'tableName' => 'tx_verowa_room',
    ],
];
