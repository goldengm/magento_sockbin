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

class Webtex_Fba_Block_Adminhtml_QueryLog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        $this->setId('fba_query_grid');
        $this->_controller = 'adminhtml_index';
        $this->setUseAjax(true);

        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {
        /** @var $collection Webtex_Fba_Model_Mws_Resource_Query_Collection */
        $collection = Mage::getModel('mws/query')->getCollection()
            ->addFieldToFilter('status', array('neq' => Webtex_Fba_Model_Mws_Query::STATUS_THROTTLED));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => Mage::helper('fba')->__('ID'),
            'align' => 'right',
            'width' => '20px',
            'filter_index' => 'id',
            'index' => 'id'
        ));

        $this->addColumn('request_id', array(
            'header' => Mage::helper('fba')->__('Amazon Request Id'),
            'filter_index' => 'request_id',
            'align' => 'center',
            'width' => '250px',
            'index' => 'request_id'
        ));

        $this->addColumn('class', array(
            'header' => Mage::helper('fba')->__('Class'),
            'align' => 'left',
            'filter_index' => 'class',
            'index' => 'class',
            'type' => 'options',
            'options' => Webtex_Fba_Model_Mws_Query::getClassesOptions(),
        ));

        $this->addColumn('method', array(
            'header' => Mage::helper('fba')->__('Method'),
            'align' => 'left',
            'filter_index' => 'method',
            'index' => 'method',
            'type' => 'options',
            'options' => Webtex_Fba_Model_Mws_Query::getMethodsOptions(),
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('fba')->__('Status'),
            'align' => 'left',
            'filter_index' => 'status',
            'index' => 'status',
            'width' => '20px',
            'type' => 'options',
            'options' => Webtex_Fba_Model_Mws_Query::getStatusOptions(),
            'renderer' => 'fba/adminhtml_queryLog_renderer_status',
        ));

        $this->addColumn('create_date', array(
            'header' => Mage::helper('fba')->__('Create Date'),
            'align' => 'left',
            'filter_index' => 'create_date',
            'width' => '150px',
            'index' => 'create_date',
            'type' => 'datetime',
        ));

        $this->addColumn('execution_time', array(
            'header' => Mage::helper('fba')->__('Execution Time'),
            'align' => 'left',
            'width' => '20px',
            'filter_index' => 'execution_time',
            'index' => 'execution_time',
        ));

        $this->addColumn('last_execution_date', array(
            'header' => Mage::helper('fba')->__('Last Execution Date'),
            'align' => 'left',
            'filter_index' => 'last_execution_date',
            'index' => 'last_execution_date',
            'width' => '150px',
            'type' => 'datetime',
        ));

        $this->addColumn('error_message', array(
            'header' => Mage::helper('fba')->__('Error Message'),
            'align' => 'left',
            'filter_index' => 'error_message',
            'index' => 'error_message',
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

        $this->addColumn('action', array(
            'header' => Mage::helper('fba')->__('Details'),
            'width' => '50px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('fba')->__('Details'),
                    'url' => array(
                        'base' => '*/*/amazonQueryDetails',
                    ),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'id',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/queryGrid', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/amazonQueryDetails', array('id' => $row->getId()));
    }
}