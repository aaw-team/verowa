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
        'title' => 'Room',
        'label' => 'room_name',
        'hideTable' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'searchFields' => 'room_name,shortcut,location_name,street,postcode,city',
    ],
    'interface' => [],
    'columns' => [
        'room_id' => [
            'label' => 'room_id',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
        'room_name' => [
            'label' => 'room_name',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
        'shortcut' => [
            'label' => 'shortcut',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
        'location_id' => [
            'label' => 'location_id',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
        'location_name' => [
            'label' => 'location_name',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
        'street' => [
            'label' => 'street',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
        'postcode' => [
            'label' => 'postcode',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
        'city' => [
            'label' => 'city',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
        'location_url' => [
            'label' => 'location_url',
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
        'location_url_is_external' => [
            'label' => 'location_url_is_external',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [[0 => '', 1 => '']],
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    room_id,
                    room_name,
                    shortcut,
                    location_id,
                    location_name,
                    street,
                    postcode,
                    city,
                    location_url,
                    location_url_is_external,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
            ',
        ],
    ],
];
