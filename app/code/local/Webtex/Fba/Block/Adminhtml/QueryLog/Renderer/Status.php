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

class Webtex_Fba_Block_Adminhtml_QueryLog_Renderer_Status
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $options = Webtex_Fba_Model_Mws_Query::getStatusOptions();
        $index = $row->getData($this->getColumn()->getIndex());
        if (isset($options[$index])) {
            $value = $options[$index];
            $colors[Webtex_Fba_Model_Mws_Query::STATUS_FAULT] = '#F50035';
            $colors[Webtex_Fba_Model_Mws_Query::STATUS_THROTTLED] = '#F2D933';
            $colors[Webtex_Fba_Model_Mws_Query::STATUS_SUCCESS] = '#009C3C';
            return '<span style="color:' . $colors[$index] . ';">' . $value . '</span>';
        } else
            return '';
    }

}