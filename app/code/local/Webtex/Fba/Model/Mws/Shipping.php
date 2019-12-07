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

/**
 * amazon query model
 * Table: 'fba_mws_queries'
 * Fields:
 *  - id - primary key
 *  - fba_marketplace_id - foreign key to mws/marketplace table
 *  - is_active - boolean active/inactive state field
 *  - title - text, shipping method custom title
 *  - rules - text field with serialized price rules
 *  - country - text field with coma-separated country codes
 *  - allow_specific_country - boolean
 *  - is_default - boolean, is shipping method default for fba carrier
 *  - type - shipping type from Webtex_Fba_Model_Shipping_Carrier_Fba_Source_Type
 *
 *
 * methods:
 * @method int getId()
 * @method Webtex_Fba_Model_Mws_Shipping setIsActive(boolean)
 * @method boolean getIsActive()
 * @method Webtex_Fba_Model_Mws_Shipping setFbaMarketplaceId(int)
 * @method int getFbaMarketplaceId()
 * @method Webtex_Fba_Model_Mws_Shipping setTitle(string)
 * @method string getTitle()
 * @method Webtex_Fba_Model_Mws_Shipping setRules(string)
 * @method Webtex_Fba_Model_Mws_Shipping setCountry(string)
 * @method string getCountry()
 * @method Webtex_Fba_Model_Mws_Shipping setAllowSpecificCountry(boolean)
 * @method boolean getAllowSpecificCountry()
 * @method Webtex_Fba_Model_Mws_Shipping setIsDefault(boolean)
 * @method boolean getIsDefault()
 * @method Webtex_Fba_Model_Mws_Shipping setType(int)
 * @method int getType()
 *
 */

class Webtex_Fba_Model_Mws_Shipping extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('mws/shipping');
    }

    protected function _beforeSave()
    {
        if (is_array($this->getData('rules')))
            $this->setRules(serialize($this->getData('rules')));
        if (is_array($this->getCountry()))
            $this->setCountry(implode(',', $this->getCountry()));
        return parent::_beforeSave();
    }

    public function getRules()
    {
        if ($this->getData('rules') != "" && !is_array($this->getData('rules')))
            $this->setData('rules', unserialize($this->getData('rules')));
        return $this->getData('rules');
    }

    protected function _afterLoad()
    {
        $this->setData('rules', unserialize($this->getData('rules')));
        $this->setCountry(explode(',', $this->getCountry()));
        return parent::_beforeSave();
    }

}