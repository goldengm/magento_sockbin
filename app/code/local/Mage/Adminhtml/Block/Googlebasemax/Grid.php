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
 * Googlebasemaxs grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Googlebasemax_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('googlebasemaxGrid');
        $this->setDefaultSort('googlebasemax_id');

    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('googlebasemax/googlebasemax')->getCollection();
        /* @var $collection Mage_Googlebasemax_Model_Mysql4_Googlebasemax_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('googlebasemax_id', array(
            'header'    => Mage::helper('googlebasemax')->__('ID'),
            'width'     => '50px',
            'index'     => 'googlebasemax_id'
        ));

        $this->addColumn('googlebasemax_filename', array(
            'header'    => Mage::helper('googlebasemax')->__('Filename'),
            'index'     => 'googlebasemax_filename'
        ));

        $this->addColumn('googlebasemax_path', array(
            'header'    => Mage::helper('googlebasemax')->__('Path'),
            'index'     => 'googlebasemax_path'
        ));

        $this->addColumn('link', array(
            'header'    => Mage::helper('googlebasemax')->__('Link for Google Base'),
            'index'     => 'concat(googlebasemax_path, googlebasemax_filename)',
            'renderer'  => 'adminhtml/googlebasemax_grid_renderer_link',
        ));

        $this->addColumn('googlebasemax_time', array(
            'header'    => Mage::helper('googlebasemax')->__('Last Time Generated'),
            'width'     => '150px',
            'index'     => 'googlebasemax_time',
            'type'      => 'datetime',
        ));


        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('googlebasemax')->__('Store View'),
                'index'     => 'store_id',
                'type'      => 'store',
            ));
        }

        $this->addColumn('action', array(
            'header'   => Mage::helper('googlebasemax')->__('Action'),
            'filter'   => false,
            'sortable' => false,
            'width'    => '100',
            'renderer' => 'adminhtml/googlebasemax_grid_renderer_action'
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
        return $this->getUrl('*/*/edit', array('googlebasemax_id' => $row->getId()));
    }

}
