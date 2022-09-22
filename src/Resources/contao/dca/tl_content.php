<?php

declare(strict_types=1);

/*
 * Palettes
 */

// buttons palette
// panel palettes
$GLOBALS['TL_DCA']['tl_content']['metapalettes']['bs_panel_group_start'] = [
    'type'           => ['type', 'bs_panel_name', 'headline'],
    'template'       => [':hide', 'customTpl'],
    'protected'      => [':hide', 'protected'],
    'expert'         => [':hide', 'guests', 'cssID'],
    'invisible'      => ['invisible', 'start', 'stop'],
];

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['bs_panel_group_end'] = [
    'type'           => ['type'],
    'template'       => [':hide', 'customTpl'],
    'protected'      => [':hide', 'protected'],
    'expert'         => [':hide', 'guests', 'cssID'],
    'invisible'      => ['invisible', 'start', 'stop'],
];

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['bs_panel_start'] = [
    'type'           => ['type', 'headline'],
    'config'         => ['bs_panel_group', 'bs_expanded'],
    'template'       => [':hide', 'customTpl'],
    'protected'      => [':hide', 'protected'],
    'expert'         => [':hide', 'guests', 'cssID'],
    'invisible'      => ['invisible', 'start', 'stop'],
];

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['bs_panel_end'] = [
    'type'           => ['type'],
    'template'       => [':hide', 'customTpl'],
    'protected'      => [':hide', 'protected'],
    'expert'         => [':hide', 'guests', 'cssID'],
    'invisible'      => ['invisible', 'start', 'stop'],
];

$GLOBALS['TL_DCA']['tl_content']['metapalettes']['bs_panel_single'] = [
    'type'           => ['type', 'headline'],
    'text'           => ['text'],
    'image'          => ['addImage'],
    'config'         => ['bs_panel_group', 'bs_expanded'],
    'template'       => [':hide', 'customTpl'],
    'protected'      => [':hide', 'protected'],
    'expert'         => [':hide', 'guests', 'cssID'],
    'invisible'      => ['invisible', 'start', 'stop'],
];

/*
 * Fields
 */

$GLOBALS['TL_DCA']['tl_content']['fields']['bs_expanded'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_content']['bs_expanded'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'default'   => false,
    'eval'      => ['tl_class' => 'w50 m12'],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['bs_panel_name'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_content']['bs_panel_name'],
    'exclude'       => true,
    'inputType'     => 'text',
    'reference'     => &$GLOBALS['TL_LANG']['tl_content'],
    'eval'          => [
        'includeBlankOption' => true,
        'chosen'             => true,
        'tl_class'           => 'w50',
    ],
    'save_callback' => [
        ['contao_bootstrap.panel.listener.dca.content', 'generatePanelName'],
    ],
    'sql'           => "varchar(64) NOT NULL default ''",
];
