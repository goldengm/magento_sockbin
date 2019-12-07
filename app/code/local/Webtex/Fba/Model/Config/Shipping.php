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

class Webtex_Fba_Model_Config_Shipping extends Mage_Core_Model_Config_Data
{
    const SHIPPING_TITLE_KEY_PATH = 'fba/mws_settings/shipping_title';
    const CARRIER_PATH = 'carriers/fbashipping/title';

    /**
     * Cron settings after save
     *
     * @return none
     */
    protected function _afterSave()
    {
        try {

            Mage::getModel('core/config')->saveConfig(self::CARRIER_PATH,$this->getValue(), $this->getScope(), $this->getScopeId());
        } catch (Exception $e) {
            Mage::throwException(Mage::helper('adminhtml')->__('Unable to save the Carrier Name.'));
        }
    }
}
