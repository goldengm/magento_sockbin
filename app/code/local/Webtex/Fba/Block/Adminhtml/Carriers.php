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

class Webtex_Fba_Block_Adminhtml_Carriers extends Mage_Adminhtml_Block_Template
{
    public function _construct()
    {
        $request = $this->getRequest()->getParams();
        if (!array_key_exists('isAjax', $request)) {
            $request['isAjax'] = 0;
        }
        $this->setAjax($request['isAjax']);
        if (array_key_exists('active_id', $request)
            && $request['active_id']
        ) {
            $this->setActiveInfo(
                Mage::getModel('mws/tracking')
                    ->getCollection()
                    ->setOrder('id', 'DESC')->getItems()
            );

            $this->setActiveId($request['active_id']);
        }
        if ($request['isAjax']) {
            $this->setData('template', 'webtex/fba/carriers.phtml');
        }
        parent::_construct();
    }

    public function getCarriersData()
    {
        return Mage::getModel('mws/tracking')->getCollection()
            ->setOrder('id', 'DESC')->getItems();
    }

    public function getAjaxUrl()
    {
        return $this->getUrl('*/*/ajaxCariers', array('_current' => true));
    }

    public function getSyncWebsiteUrl()
    {
        return $this->getUrl('*/*/saveCarriers', array('_current' => true));
    }

    /**
     * Retrieve
     *
     * @return unknown
     */
    public function getCarriers()
    {
        $carriers = array();
        $carrierInstances = Mage::getSingleton('shipping/config')->getAllCarriers();
        $carriers['custom'] = Mage::helper('sales')->__('Custom Value');
        foreach ($carrierInstances as $code => $carrier) {
            if ($carrier->isTrackingAvailable()) {
                $carriers[$code] = $carrier->getConfigData('title');
            }
        }
        return $carriers;
    }
}