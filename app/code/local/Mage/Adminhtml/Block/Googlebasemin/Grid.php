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
 * Googlebasemins grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Googlebasemin_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('googlebaseminGrid');
        $this->setDefaultSort('googlebasemin_id');

    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('googlebasemin/googlebasemin')->getCollection();
        /* @var $collection Mage_Googlebasemin_Model_Mysql4_Googlebasemin_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('googlebasemin_id', array(
            'header'    => Mage::helper('googlebasemin')->__('ID'),
            'width'     => '50px',
            'index'     => 'googlebasemin_id'
        ));

        $this->addColumn('googlebasemin_filename', array(
            'header'    => Mage::helper('googlebasemin')->__('Filename'),
            'index'     => 'googlebasemin_filename'
        ));

        $this->addColumn('googlebasemin_path', array(
            'header'    => Mage::helper('googlebasemin')->__('Path'),
            'index'     => 'googlebasemin_path'
        ));

        $this->addColumn('link', array(
            'header'    => Mage::helper('googlebasemin')->__('Link for Google Base Min'),
            'index'     => 'concat(googlebasemin_path, googlebasemin_filename)',
            'renderer'  => 'adminhtml/googlebasemin_grid_renderer_link',
        ));

        $this->addColumn('googlebasemin_time', array(
            'header'    => Mage::helper('googlebasemin')->__('Last Time Generated'),
            'width'     => '150px',
            'index'     => 'googlebasemin_time',
            'type'      => 'datetime',
        ));


        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('googlebasemin')->__('Store View'),
                'index'     => 'store_id',
                'type'      => 'store',
            ));
        }

        $this->addColumn('action', array(
            'header'   => Mage::helper('googlebasemin')->__('Action'),
            'filter'   => false,
            'sortable' => false,
            'width'    => '100',
            'renderer' => 'adminhtml/googlebasemin_grid_renderer_action'
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
        return $this->getUrl('*/*/edit', array('googlebasemin_id' => $row->getId()));
    }

}
