<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2018
 * @package    MenAtWork\ContentSelectionBundle\Contao
 * @license    GNU/LGPL
 * @filesource
 */

namespace MenAtWork\ContentSelectionBundle\Contao;

/**
 * Class ContentSelection
 */
class ContentSelection extends \Backend
{
    /**
     * @param \ContentModule $objRow
     * @param string $strBuffer
     * @return string | NULL
     */
    public function getContentElementWithPermission($objRow, $strBuffer)
    {
        if ($objRow->contentSelection == '' || TL_MODE == 'BE')
            return $strBuffer;

        $arrCs = deserialize($objRow->contentSelection);

        if (!is_array($arrCs))
            return $strBuffer;

        $objUa =  \Environment::get('agent');

        $blnGlobalPermisson = false;
        foreach ($arrCs as $arrSelector)
        {
            $arrSelector['cs_client_os'] = ($arrSelector['cs_client_os'] != '') ? array(
                'value' => $arrSelector['cs_client_os'],
                'config' => $GLOBALS['TL_CONFIG']['os'][$arrSelector['cs_client_os']]
                    ) : false;
            $arrSelector['cs_client_browser']   = ($arrSelector['cs_client_browser'] != '') ? $GLOBALS['TL_CONFIG']['browser'][$arrSelector['cs_client_browser']] : false;
            $arrSelector['cs_client_is_mobile'] = (($arrSelector['cs_client_is_mobile'] != '') ? (($arrSelector['cs_client_is_mobile'] == 1) ? true : false) : 'empty');

            $blnPermisson = true;
            foreach ($arrSelector as $strConfig => $mixedConfig)
            {
                switch ($strConfig)
                {
                    case 'cs_client_os':
                        $blnPermisson = ($blnPermisson && (strtolower($mixedConfig['config']['os']) == $objUa->os || $mixedConfig['config']['os'] == '')) ? true : false ;
                        break;

                    case 'cs_client_browser':
                        $blnPermisson = ($blnPermisson && (strtolower($mixedConfig['browser']) == $objUa->browser || $mixedConfig['browser'] == '')) ? true : false;
                        break;

                    case 'cs_client_browser_version':
                        $blnPermisson = ($blnPermisson && $this->checkBrowserVerPermission($mixedConfig, $objUa, $arrSelector['cs_client_browser_operation']));
                        break;

                    case 'cs_client_is_mobile':
                        if (strlen($mixedConfig) < 2)
                        {
                            $blnPermisson = ($blnPermisson && $mixedConfig == $objUa->mobile) ? true : false;
                        }
                        break;

                    case 'cs_client_is_invert':
                        if ($mixedConfig)
                        {
                            $blnPermisson = ($blnPermisson) ? false : true;
                        }
                        break;
                }
            }

            if (!$blnGlobalPermisson && $blnPermisson)
            {
                $blnGlobalPermisson = true;
            }
        }

        if ($blnGlobalPermisson === false)
        {
            return;
        }
        else
        {
            return $strBuffer;
        }
    }

    /**
     * Return option array for operation systems
     *
     * @return array
     */
    public function getClientOs()
    {
        $arrOptions = array();

        foreach ($GLOBALS['TL_CONFIG']['os'] as $strLabel => $arrOs)
        {
            $arrOptions[$strLabel] = $strLabel;
        }

        return $arrOptions;
    }

    /**
     * Return browser array for operation systems
     *
     * @return array
     */
    public function getClientBrowser()
    {
        $arrOptions = array();

        foreach ($GLOBALS['TL_CONFIG']['browser'] as $strLabel => $arrBrowser)
        {
            $arrOptions[$strLabel] = $strLabel;
        }

        return $arrOptions;
    }

    /**
     * Check if the browser version has permission
     *
     * @param mixed $mixedConfig
     * @param $objUa
     * @param string $strOperator
     * @return boolean
     */
    public function checkBrowserVerPermission($mixedConfig, $objUa, $strOperator)
    {
        if($mixedConfig != '')
        {
            switch ($strOperator)
            {
                case 'lt':
                    if($objUa->version < $mixedConfig) return true;
                    break;
                case 'lte':
                    if($objUa->version <= $mixedConfig) return true;
                    break;
                case 'gte':
                    if($objUa->version >= $mixedConfig) return true;
                    break;
                case 'gt':
                    if($objUa->version > $mixedConfig) return true;
                    break;
                default:
                    if($objUa->version == $mixedConfig) return true;
                    break;
            }
            return false;
        }

        return true;
    }

}

?>