<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2018
 * @package    MenAtWork\ContentSelectionBundle
 * @license    GNU/LGPL
 * @filesource
 */

/**
 * Hooks
 *
 */
$GLOBALS['TL_HOOKS']['getContentElement'][] = array('MenAtWork\\ContentSelectionBundle\\Contao\\ContentSelection', 'getContentElementWithPermission');

