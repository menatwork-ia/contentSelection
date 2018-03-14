<?php
/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2018
 * @package    MenAtWork\ContentSelectionBundle\Contao\Controller
 * @license    GNU/LGPL
 * @filesource
 */

namespace MenAtWork\ContentSelectionBundle\Contao\Controller;

class ContentSelectController extends \Controller
{
    /**
     * Return the new label for the content element
     *
     * @param array $arrRow
     * @return string
     */
    public function childRecordCallback($arrRow)
    {
        $arrContentSelection = array();
        if ($arrRow['contentSelection'])
        {
            $arrCs = deserialize($arrRow['contentSelection']);
            if (is_array($arrCs))
            {
                $arrSelector = $arrCs[0];
                $strInvert   = (($arrSelector['cs_client_is_invert']) ? ucfirst($GLOBALS['TL_LANG']['MSC']['hiddenHide']) : ucfirst($GLOBALS['TL_LANG']['MSC']['hiddenShow'])) . ':';
                foreach ($arrSelector as $strConfig => $mixedConfig)
                {
                    switch ($strConfig)
                    {
                        case 'cs_client_os':
                            if ($mixedConfig)
                            {
                                $arrContentSelection[] = ' ' . $mixedConfig;
                            }
                            break;

                        case 'cs_client_browser':
                            if ($mixedConfig)
                            {
                                $arrContentSelection[] = ' ' . $mixedConfig;
                            }
                            break;

                        case 'cs_client_browser_version':
                            if ($mixedConfig)
                            {
                                switch ($arrSelector['cs_client_browser_operation'])
                                {
                                    case 'lt':
                                        $strOperator           = '<';
                                        break;
                                    case 'lte':
                                        $strOperator           = '<=';
                                        break;
                                    case 'gte':
                                        $strOperator           = '>=';
                                        break;
                                    case 'gt':
                                        $strOperator           = '>';
                                        break;
                                    default:
                                        $strOperator           = '';
                                        break;
                                }
                                $arrContentSelection[] = ' ' . $strOperator . ' ' . $mixedConfig;
                            }
                            break;

                        case 'cs_client_is_mobile':
                            if ($mixedConfig != '')
                            {
                                $arrContentSelection[] = ' ' . $GLOBALS['TL_LANG']['tl_content']['cs_client_is_mobile'][0] . ': ' . (($mixedConfig == 1) ? $GLOBALS['TL_LANG']['MSC']['yes'] : $GLOBALS['TL_LANG']['MSC']['no']);
                            }
                            break;
                    }
                }

                if (count($arrContentSelection) > 0)
                {
                    array_unshift($arrContentSelection, $strInvert);
                    array_unshift($arrContentSelection, '(');
                    if (count($arrCs) > 1)
                    {
                        $arrContentSelection[] = ' /... ';
                    }
                    $arrContentSelection[] = ')';
                }
            }
        }

        return vsprintf(
            '<div class="cte_type %s">%s%s %s</div><div class="limit_height%s">%s</div>', array(
                $arrRow['invisible'] ? 'unpublished' : 'published',
                ($GLOBALS['TL_LANG']['CTE'][$arrRow['type']][0] != '') ? $GLOBALS['TL_LANG']['CTE'][$arrRow['type']][0] : '&nbsp;',
                (($arrRow['type'] == 'alias') ? ' ID ' . $arrRow['cteAlias'] : '') . ($arrRow['protected'] ? ' (' . $GLOBALS['TL_LANG']['MSC']['protected'] . ')' : ($arrRow['guests'] ? ' (' . $GLOBALS['TL_LANG']['MSC']['guests'] . ')' : '')),
                implode('', $arrContentSelection),
                (!$GLOBALS['TL_CONFIG']['doNotCollapse'] ? ' h64' : ''),
                $this->getContentElement($arrRow['id'])
            )
        );
    }
}