<?php

declare(strict_types=1);

/*
 * Palettes
 */

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['bs_accordion_group_start'] = [
    'type'           => ['type', 'bs_accordion_name', 'headline'],
    'template'       => [':hide', 'customTpl'],
    'protected'      => [':hide', 'protected'],
    'expert'         => [':hide', 'guests', 'cssID'],
    'invisible'      => ['invisible', 'start', 'stop'],
];

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['bs_accordion_group_end'] = [
    'type'           => ['type'],
    'template'       => [':hide', 'customTpl'],
    'protected'      => [':hide', 'protected'],
    'expert'         => [':hide', 'guests', 'cssID'],
    'invisible'      => ['invisible', 'start', 'stop'],
];

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['bs_accordion_start'] = [
    'type'           => ['type', 'headline'],
    'config'         => ['bs_accordion_group', 'bs_expanded'],
    'template'       => [':hide', 'customTpl'],
    'protected'      => [':hide', 'protected'],
    'expert'         => [':hide', 'guests', 'cssID'],
    'invisible'      => ['invisible', 'start', 'stop'],
];

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['bs_accordion_end'] = [
    'type'           => ['type'],
    'template'       => [':hide', 'customTpl'],
    'protected'      => [':hide', 'protected'],
    'expert'         => [':hide', 'guests', 'cssID'],
    'invisible'      => ['invisible', 'start', 'stop'],
];

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['bs_accordion_single'] = [
    'type'           => ['type', 'headline'],
    'text'           => ['text'],
    'image'          => ['addImage'],
    'config'         => ['bs_accordion_group', 'bs_expanded'],
    'template'       => [':hide', 'customTpl'],
    'protected'      => [':hide', 'protected'],
    'expert'         => [':hide', 'guests', 'cssID'],
    'invisible'      => ['invisible', 'start', 'stop'],
];

/*
 * Fields
 */

$GLOBALS['TL_DCA']['tl_content']['fields']['bs_expanded'] = [
    'exclude'   => true,
    'inputType' => 'checkbox',
    'default'   => false,
    'eval'      => ['tl_class' => 'w50 m12'],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['bs_accordion_name'] = [
    'exclude'       => true,
    'inputType'     => 'text',
    'reference'     => &$GLOBALS['TL_LANG']['tl_content'],
    'eval'          => [
        'includeBlankOption' => true,
        'chosen'             => true,
        'tl_class'           => 'w50',
    ],
    'sql'           => "varchar(64) NOT NULL default ''",
];
