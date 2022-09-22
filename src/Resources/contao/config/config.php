<?php

declare(strict_types=1);

/*
 * Wrappers
 */

$GLOBALS['TL_WRAPPERS']['start'][] = 'bs_panel_group_start';
$GLOBALS['TL_WRAPPERS']['start'][] = 'bs_panel_start';
$GLOBALS['TL_WRAPPERS']['stop'][]  = 'bs_panel_group_end';
$GLOBALS['TL_WRAPPERS']['stop'][]  = 'bs_panel_end';
