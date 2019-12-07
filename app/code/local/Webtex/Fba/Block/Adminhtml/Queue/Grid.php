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

class Webtex_Fba_Block_Adminhtml_Queue_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        $this->setId('fba_queue_grid');
        $this->_controller = 'adminhtml_index';
        $this->setUseAjax(true);

    }

    protected function _prepareCollection()
    {
        /** @var $collection Webtex_Fba_Model_Mws_Resource_Query_Collection */
        $collection = Mage::getModel('mws/query')->getCollection();
        $collection->addFieldToFilter('priority', array('neq' => 0))
            ->addFieldToFilter('status', Webtex_Fba_Model_Mws_Query::STATUS_THROTTLED)
            ->addFieldToFilter('fba_marketplace_id', array('neq' => 0))
            ->addOrder('priority', Varien_Data_Collection::SORT_ORDER_ASC)
            ->addOrder('create_date', Varien_Data_Collection::SORT_ORDER_ASC);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => Mage::helper('fba')->__('ID'),
            'width' => '20px',
            'filter_index' => 'id',
            'index' => 'id',
            'sortable' => false
        ));

        $this->addColumn('priority', array(
            'header' => Mage::helper('fba')->__('Priority'),
            'width' => '20px',
            'filter_index' => 'priority',
            'index' => 'priority',
            'sortable' => false
        ));

        $this->addColumn('class', array(
            'header' => Mage::helper('fba')->__('Class'),
            'filter_index' => 'class',
            'index' => 'class',
            'type' => 'options',
            'options' => Webtex_Fba_Model_Mws_Query::getQueueClassesOptions(),
            'sortable' => false

        ));

        $this->addColumn('method', array(
            'header' => Mage::helper('fba')->__('Method'),
            'filter_index' => 'method',
            'index' => 'method',
            'type' => 'options',
            'options' => Webtex_Fba_Model_Mws_Query::getQueueMethodsOptions(),
            'sortable' => false
        ));

        $this->addColumn('create_date', array(
            'header' => Mage::helper('fba')->__('Create Date'),
            'filter_index' => 'create_date',
            'index' => 'create_date',
            'type' => 'datetime',
            'sortable' => false
        ));

        $this->addColumn('fba_marketplace_id', array(
            'header' => Mage::helper('fba')->__('Marketplace'),
            'filter_index' => 'fba_marketplace_id',
            'index' => 'fba_marketplace_id',
            'renderer' => 'fba/adminhtml_marketplace_renderer_id',
            'type' => 'options',
            'options' => Mage::getModel('mws/marketplace')->getOptionArray(),
            'sortable' => false
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('query');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => Mage::helper('fba')->__('Delete'),
            'url'      => $this->getUrl('*/*/queueMassDelete'),
            'confirm'  => Mage::helper('fba')->__('Are you sure?')
        ));

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/queueGrid', array('_current' => true));
    }
}