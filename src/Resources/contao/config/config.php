<?php

declare(strict_types=1);

/*
 * Wrappers
 */

$GLOBALS['TL_WRAPPERS']['start'][] = 'bs_accordion_group_start';
$GLOBALS['TL_WRAPPERS']['start'][] = 'bs_accordion_start';
$GLOBALS['TL_WRAPPERS']['stop'][]  = 'bs_accordion_group_end';
$GLOBALS['TL_WRAPPERS']['stop'][]  = 'bs_accordion_end';
