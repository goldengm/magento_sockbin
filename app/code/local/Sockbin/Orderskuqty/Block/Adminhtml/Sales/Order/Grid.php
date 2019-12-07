<?php
/**
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 * @category    Magemaven
 * @package     Magemaven_OrderComment
 * @copyright   Copyright (c) 2011-2012 Sergey Storchay <r8@r8.com.ua>
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Sockbin_Orderskuqty_Block_Adminhtml_Sales_Order_Grid extends Magemaven_OrderComment_Block_Adminhtml_Sales_Order_Grid
{
    /**
     * Columns, that become ambiguous after join
     *
     * @var array
     */
    protected $_ambiguousColumns = array(
        'status',
        'created_at',
    );

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'ordercomment/order_grid_collection';
    }

    /**
     * Prepare grid columns
     *
     * @return Magemaven_OrderComment_Block_Adminhtml_Sales_Order_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        // Add order comment to grid
        $this->addColumnAfter('entity_id', array(
            'header'       => Mage::helper('sales')->__('Items'),
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'Sockbin_Orderskuqty_Block_Adminhtml_Sales_Order_Grid_Renderer',
        ),'status');

        $this->sortColumnsByOrder();

        return $this;
    }

}
