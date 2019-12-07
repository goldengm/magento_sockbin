<?php
/**
 * Webtex
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.webtexsoftware.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@webtexsoftware.com and we will send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.webtexsoftware.com for more information,
 * or contact us through this email: info@webtexsoftware.com.
 *
 * @category   Webtex
 * @package    Webtex_Fba
 * @copyright  Copyright (c) 2011 Webtex Solutions, LLC (http://www.webtexsoftware.com/)
 * @license    http://www.webtexsoftware.com/LICENSE.txt End-User License Agreement
 */

class Webtex_Fba_Block_Adminhtml_Marketplace_Renderer_Status
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    static public function getStatusOptions()
    {

        $options = array();
        foreach (Mage::getModel('adminhtml/system_config_source_enabledisable')->toOptionArray() as $option)
            $options[$option['value']] = $option['label'];

        return $options;
    }

    public function render(Varien_Object $row)
    {
        $options = $this->getColumn()->getOptions();
        $index = $row->getData($this->getColumn()->getIndex());
        $value = $options[$index];
        $colors[0] = '#F50035';
        $colors[1] = '#009C3C';
        return '<span style="color:' . $colors[$index] . ';">' . $value . '</span>';
    }

}