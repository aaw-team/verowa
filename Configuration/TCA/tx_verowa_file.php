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
    'ctrl' => [
        'title' => 'File',
        'label' => 'file_name',
        'hideTable' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'searchFields' => 'file_name,desc',
    ],
    'interface' => [],
    'columns' => [
        'event' => [
            'label' => 'event',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_verowa_event',
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],
        'file_name' => [
            'label' => 'file_name',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
            ],
        ],
        'desc' => [
            'label' => 'desc',
            'config' => [
                'type' => 'text',
            ],
        ],
        'url' => [
            'label' => 'url',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim',
                'fieldControl' => [
                    'linkPopup' => [
                        'options' => [
                            'blindLinkFields' => 'class, params, target, title',
                            'blindLinkOptions' => 'file, folder, mail, page, spec, telephone',
                        ],
                    ],
                ],
            ],
        ],
        'filesize_kb' => [
            'label' => 'filesize_kb',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
            ],
        ],
        'file_type' => [
            'label' => 'file_type',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    event,
                    file_name,
                    desc,
                    url,
                    filesize_kb,
                    file_type,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
            ',
        ],
    ],
];
