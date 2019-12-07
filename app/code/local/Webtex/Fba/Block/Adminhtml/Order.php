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

class Webtex_Fba_Block_Adminhtml_Order
    extends Mage_Adminhtml_Block_Sales_Order_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    private $_query = null;

    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }


    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Fulfillment By Amazon');
    }

    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Fba Order Information');
    }

    public function canShowTab()
    {
        return $this->getOrder()->getFbaMarketplaceId();
    }

    public function isHidden()
    {
        return !$this->getOrder()->getFbaMarketplaceId();
    }

    /**
     * Prepare child blocks
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Invoice_Create_Items
     */
    protected function _prepareLayout()
    {
        $sendButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id' => 'send_query_button',
                'label' => Mage::helper('sales')->__('Send Query To Amazon'),
                'class' => 'save',
                'on_click' => 'setLocation(\'' . $this->getSendQueryUrl() . '\')'
            ));
        $this->setChild('send_query_button', $sendButton);
        $unblockButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id' => 'unblock_all_btn',
                'label' => Mage::helper('fba')->__('Unblock Everything'),
                'class' => 'delete',
                'on_click' => 'setLocation(\'' . $this->getUnblockQtyUrl() . '\')'
            ));
        $this->setChild('unblock_all_btn', $unblockButton);
        if ($this->getQuery()) {
            $resendButton = $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'id' => 'resend_query_button',
                    'label' => Mage::helper('sales')->__('Resend Query To Amazon'),
                    'class' => 'save',
                    'on_click' => 'setLocation(\'' . $this->getSendQueryUrl() . '\')'
                ));
            $this->setChild('resend_query_button', $resendButton);
        }
        return parent::_prepareLayout();
    }

    /**
     * @return Webtex_Fba_Model_Mws_Query|null
     */
    public function getQuery()
    {
        if ($this->_query == null && $this->getOrder()->getFbaQueryId()) {
            $this->_query = Mage::getModel('mws/query')->load($this->getOrder()->getFbaQueryId());
        }
        return $this->_query;
    }

    public function getQueryUrl()
    {
        return $this->getUrl('fba/adminhtml_index/amazonQueryDetails', array('id' => $this->getQuery()->getId()));

    }

    public function getQueryStatus()
    {
        $array = $this->getQuery()->getStatusOptions();
        return $array[$this->getQuery()->getStatus()];
    }

    public function getSendQueryUrl()
    {
        return $this->getUrl('fba/adminhtml_index/sendOrderQuery', array('order_id' => $this->getOrder()->getEntityId()));
    }

    public function getUnblockQtyUrl()
    {
        return $this->getUrl('fba/adminhtml_index/unblockQty', array('order_id' => $this->getOrder()->getEntityId()));
    }

    /**
     * @return array
     */
    public function getBlockedQty()
    {
        if ($this->getOrder()->getFbaMarketplaceId()
            && (
                !$this->getQuery()
                || $this->getQuery()->getStatus()
                    != Webtex_Fba_Model_Mws_Query::STATUS_SUCCESS
            )
        ) {
            return unserialize($this->getOrder()->getBlockedQty());
        }

        return array();
    }
}
