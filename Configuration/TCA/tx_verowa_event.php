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
        'title' => 'Event',
        'label' => 'title',
        'label_alt' => 'date_from',
        'label_alt_force' => true,
        'hideTable' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'searchFields' => 'title,topic,short_desc,long_desc',
    ],
    'interface' => [],
    'columns' => [
        'event_id' => [
            'label' => 'event_id',
            'config' => [
                'type' => 'input',
            ],
        ],
        'date_from' => [
            'label' => 'date_from',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
            ],
        ],
        'date_to' => [
            'label' => 'date_to',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
            ],
        ],
        'hide_time' => [
            'label' => 'hide_time',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [[0 => '', 1 => '']],
            ],
        ],
        'date_text' => [
            'label' => 'date_text',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
        'title' => [
            'label' => 'title',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim,required'
            ],
        ],
        'topic' => [
            'label' => 'topic',
            'config' => [
                'type' => 'text',
            ],
        ],
        'short_desc' => [
            'label' => 'short_desc',
            'config' => [
                'type' => 'text',
            ],
        ],
        'long_desc' => [
            'label' => 'long_desc',
            'config' => [
                'type' => 'text',
            ],
        ],
        'organizer' => [
            'label' => 'organizer',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'coorganizers' => [
            'label' => 'organizer',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'further_coorganizers' => [
            'label' => 'further_coorganizers',
            'config' => [
                'type' => 'text',
            ],
        ],
        'lectors' => [
            'label' => 'lectors',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'visitators' => [
            'label' => 'visitators',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'organists' => [
            'label' => 'organists',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'vergers' => [
            'label' => 'vergers',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'catering' => [
            'label' => 'date_text',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
        'with_sacrament' => [
            'label' => 'with_sacrament',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [[0 => '', 1 => '']],
            ],
        ],
        'childcare_id' => [
            'label' => 'childcare_id',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
        'childcare_text' => [
            'label' => 'childcare_id',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
        'childcare_person' => [
            'label' => 'childcare_person',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'subscribe_date' => [
            'label' => 'subscribe_date',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
            ],
        ],
        'subscribe_person' => [
            'label' => 'subscribe_person',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'baptism_offer_id' => [
            'label' => 'baptism_offer_id',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
        'baptism_offer_text' => [
            'label' => 'baptism_offer_text',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
        'collection' => [
            'label' => 'collection',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'target_groups' => [
            'label' => 'target_groups',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'layers' => [
            'label' => 'layers',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'rooms' => [
            'label' => 'rooms',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_verowa_room',
                'MM' => 'tx_verowa_event_room_mm',
            ],
        ],
        'files' => [
            'label' => 'files',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'image_url' => [
            'label' => 'image_url',
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
        'image_width' => [
            'label' => 'image_width',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
        'image_height' => [
            'label' => 'image_height',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'max' => 255,
                'eval' => 'trim'
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    event_id,
                    date_from,
                    date_to,
                    hide_time,
                    date_text,
                    title,
                    topic,
                    short_desc,
                    long_desc,
                    organizer,
                    coorganizers,
                    further_coorganizers,
                    lectors,
                    visitators,
                    organists,
                    vergers,
                    catering,
                    with_sacrament,
                    childcare_id,
                    childcare_text,
                    childcare_person,
                    subscribe,
                    subscribe_person,
                    baptism_offer_id,
                    baptism_offer_text,
                    collection,
                    target_groups,
                    layer,
                    rooms,
                    files,
                    image_url,
                    image_width,
                    image_height,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
            ',
        ],
    ],
];
