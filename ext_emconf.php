<?php
/*
 * Copyright by Agentur am Wasser | Maeder & Partner AG
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'Verowa',
    'description' => 'Verowa Agenda integration for TYPO3',
    'category' => 'fe',
    'author' => 'Agentur am Wasser | Maeder & Partner AG',
    'author_email' => 'development@agenturamwasser.ch',
    'state' => 'alpha',
    'clearCacheOnLoad' => true,
    'version' => '1.0.0-dev',
    'constraints' => [
        'depends' => [
            'php' => '7.2',
            'typo3' => '10.4.14-10.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
