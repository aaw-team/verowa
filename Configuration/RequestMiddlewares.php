<?php
/*
 * Copyright by Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

return [
    'frontend' => [
        'aaw-team/verowa/ics-event-download' => [
            'target' => \AawTeam\Verowa\Http\Middleware\IcsEventDownload::class,
            'before' => [
                'typo3/cms-frontend/page-resolver',
            ],
            'after' => [
                'typo3/cms-frontend/static-route-resolver',
            ],
        ],
    ],
];
