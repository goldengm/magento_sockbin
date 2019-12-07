<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Solidcommerces grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Solidcommerce_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('solidcommerceGrid');
        $this->setDefaultSort('solidcommerce_id');

    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('solidcommerce/solidcommerce')->getCollection();
        /* @var $collection Mage_Solidcommerce_Model_Mysql4_Solidcommerce_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('solidcommerce_id', array(
            'header'    => Mage::helper('solidcommerce')->__('ID'),
            'width'     => '50px',
            'index'     => 'solidcommerce_id'
        ));

        $this->addColumn('solidcommerce_filename', array(
            'header'    => Mage::helper('solidcommerce')->__('Filename'),
            'index'     => 'solidcommerce_filename'
        ));

        $this->addColumn('solidcommerce_path', array(
            'header'    => Mage::helper('solidcommerce')->__('Path'),
            'index'     => 'solidcommerce_path'
        ));

        $this->addColumn('link', array(
            'header'    => Mage::helper('solidcommerce')->__('Link for Solid Commerce'),
            'index'     => 'concat(solidcommerce_path, solidcommerce_filename)',
            'renderer'  => 'adminhtml/solidcommerce_grid_renderer_link',
        ));

        $this->addColumn('solidcommerce_time', array(
            'header'    => Mage::helper('solidcommerce')->__('Last Time Generated'),
            'width'     => '150px',
            'index'     => 'solidcommerce_time',
            'type'      => 'datetime',
        ));


        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('solidcommerce')->__('Store View'),
                'index'     => 'store_id',
                'type'      => 'store',
            ));
        }

        $this->addColumn('action', array(
            'header'   => Mage::helper('solidcommerce')->__('Action'),
            'filter'   => false,
            'sortable' => false,
            'width'    => '100',
            'renderer' => 'adminhtml/solidcommerce_grid_renderer_action'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('solidcommerce_id' => $row->getId()));
    }

}
