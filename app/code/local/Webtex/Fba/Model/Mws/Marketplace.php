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
 *  - status - enable/disable
 *  - access_key_id - 20 - character alphanumeric sequence
 *  - secret_key - 40 - character encoded sequence
 *  - merchant_id - merchant id
 *  - amazon_marketplace - int marketplace id from Webtex_Fba_Model_AmazonMarketplace model
 *  - notification_emails - comma separated emails list for Amazon shipping notifications
 *  - notify_customers - send Amazon delivery status for customers sign
 *  - carrier_title - title for FBA carrier on the frontend
 *  - send_order_immediately - if true then order query added in queue immediately after place
 *  - last_queue_execution_time
 *  - next_queue_start_time
 *  - inventory_mode - integer inventory check mode from fba/shipping_carrier_fba_source_qtycheck model
 *  - check_qty_before_place_order - boolean Check cart products qty before place order
 *  - qty_check_field - integer id amazon qty field fba/config_checkField
 *  - inventory_check_frequency - Qty check frequency from fba/config_cron_frequency
 *  - check_orders - yes/no
 *  - shipping_currency - Mage_Adminhtml_Model_System_Config_Source_Currency value
 *
 * methods:
 * @method int getId()
 * @method Webtex_Fba_Model_Mws_Marketplace setStatus(int)
 * @method int getStatus()
 * @method Webtex_Fba_Model_Mws_Marketplace setAccessKeyId(string)
 * @method string getAccessKeyId()
 * @method Webtex_Fba_Model_Mws_Marketplace setSecretKey(string)
 * @method string getSecretKey()
 * @method Webtex_Fba_Model_Mws_Marketplace setPlainSecretKey(string)
 * @method Webtex_Fba_Model_Mws_Marketplace setMerchantId(string)
 * @method string getMerchantId()
 * @method Webtex_Fba_Model_Mws_Marketplace setAmazonMarketplace(int)
 * @method int getAmazonMarketplace()
 * @method Webtex_Fba_Model_Mws_Marketplace setNotificationEmails(string)
 * @method string getNotificationEmails()
 * @method Webtex_Fba_Model_Mws_Marketplace setNotifyCustomers(boolean)
 * @method boolean getNotifyCustomers()
 * @method Webtex_Fba_Model_Mws_Marketplace setCarrierTitle(string)
 * @method string getCarrierTitle()
 * @method Webtex_Fba_Model_Mws_Marketplace setSendOrderImmediately(boolean)
 * @method boolean getSendOrderImmediately()
 * @method Webtex_Fba_Model_Mws_Marketplace setLastQueueExecutionTime(string)
 * @method string getLastQueueExecutionTime()
 * @method Webtex_Fba_Model_Mws_Marketplace setNextQueueStartTime(string)
 * @method string getNextQueueStartTime()
 * @method Webtex_Fba_Model_Mws_Marketplace setInventoryMode(int)
 * @method int getInventoryMode()
 * @method Webtex_Fba_Model_Mws_Marketplace setShipOosAsNonFba(int)
 * @method int getShipOosAsNonFba()
 * @method Webtex_Fba_Model_Mws_Marketplace setCheckQtyBeforePlaceOrder(boolean)
 * @method boolean getCheckQtyBeforePlaceOrder()
 * @method Webtex_Fba_Model_Mws_Marketplace setQtyCheckField(string)
 * @method string getQtyCheckField()
 * @method Webtex_Fba_Model_Mws_Marketplace setInventoryCheckFrequency(int)
 * @method int getInventoryCheckFrequency()
 * @method Webtex_Fba_Model_Mws_Marketplace setCheckOrders(boolean)
 * @method boolean getCheckOrders()
 * @method Webtex_Fba_Model_Mws_Marketplace setShippingCurrency(string)
 * @method string getShippingCurrency()
 * @method Webtex_Fba_Model_Mws_Resource_Marketplace_Collection getCollection()
 *
 */

class Webtex_Fba_Model_Mws_Marketplace extends Mage_Core_Model_Abstract
{

    /** @var \Webtex_Fba_Model_Mws_Shipping[]|null */
    protected $_shippingArray = null;

    /** @var Webtex_Fba_Model_Mws_Product[] */
    private $_assignedProducts = array();

    public function _construct()
    {
        parent::_construct();
        $this->_init('mws/marketplace');
    }

    protected function _beforeSave()
    {
        if ($this->getData('plain_secret_key') != "")
            $this->setSecretKey(Mage::getModel('core/encryption')->encrypt($this->getData('plain_secret_key')));

        return parent::_beforeSave();
    }

    protected function _afterSave()
    {
        if ($this->isAmazonInventoryMode() &&
            ($this->getOrigData('qty_check_field') != $this->getData('qty_check_field')
                || $this->getOrigData('inventory_mode') != $this->getData('inventory_mode')
            )
        ) {
            $productCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('fba_marketplace_id')
                ->addAttributeToFilter('fba_marketplace_id', $this->getId());
            foreach ($productCollection as $product)
                Mage::getModel('mws/product')->checkProductStock($product);

        }

        return parent::_afterSave();
    }

    protected function _afterLoad()
    {
        if ($this->getId()) {
            $codeArray = Mage::getModel('fba/config_source_amazonMarketplace')->toArray();
            $this->setCode($codeArray[$this->getAmazonMarketplace()] . "-" . $this->getId());
        }

        if ($this->getSecretKey() != '')
            $this->setPlainSecretKey(Mage::getModel('core/encryption')->decrypt($this->getSecretKey()));

        return parent::_afterLoad();
    }

    public function getPlainSecretKey()
    {
        if ($this->getData('plain_secret_key') == "" && $this->getSecretKey() != "")
            $this->setPlainSecretKey(Mage::getModel('core/encryption')->decrypt($this->getSecretKey()));
        return $this->getData('plain_secret_key');
    }

    public function getOptionArray()
    {
        $_options = array();
        $codeArray = Mage::getModel('fba/config_source_amazonMarketplace')->toArray();
        $_options[0] = 'not fba';
        foreach ($this->getCollection() as $marketplace) {
            $_options[$marketplace->getId()] = $codeArray[$marketplace->getAmazonMarketplace()] . "-" . $marketplace->getId();
        }
        return $_options;
    }

    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        // Fixed for tax_class_id and custom_design
        if (sizeof($options) > 0) foreach($options as $option) {
            if (isset($option['value']) && $option['value'] == $value) {
                return isset($option['label']) ? $option['label'] : $option['value'];
            }
        } // End
        if (isset($options[$value])) {
            return $options[$value];
        }
        return false;
    }

    public function getAllOptions()
    {
        $_options = array();
        $codeArray = Mage::getModel('fba/config_source_amazonMarketplace')->toArray();
        $_options[] = array('label' => 'not fba', 'value' => 0);
        foreach ($this->getCollection() as $marketplace) {
            $_options[] = array('label' => $codeArray[$marketplace->getAmazonMarketplace()] . "-" . $marketplace->getId(), 'value' => $marketplace->getId());
        }
        return $_options;
    }

    public function getClientConfig($additionalUrl = '')
    {
        $urlArray = Mage::getModel('fba/config_source_amazonMarketplace')->toEndpointUrlArray();
        if (array_key_exists($this->getAmazonMarketplace(), $urlArray))
            return array('ServiceURL' => $urlArray[$this->getAmazonMarketplace()] . $additionalUrl);
        else
            return false;
    }

    public function isAmazonInventoryMode()
    {
        return $this->getId() && $this->getStatus() == 1 && $this->getInventoryMode() == Webtex_Fba_Model_Config_Source_InventoryMode::AMAZON_MODE;
    }

    /**
     * @param string $code
     * @return Webtex_Fba_Model_Mws_Shipping[]|bool|Webtex_Fba_Model_Mws_Shipping
     */
    public function getShippingSettings($code = "")
    {
        if (empty($this->_shippingArray)) {
            $collection = Mage::getModel('mws/shipping')->getCollection()->addFieldToFilter('fba_marketplace_id', $this->getId());
            foreach ($collection as $shippingMethod)
                /** @var $shippingMethod Webtex_Fba_Model_Mws_Shipping */
                $this->_shippingArray[$shippingMethod->getType()] = $shippingMethod;
        }
        if ($code === "") {
            if (is_array($this->_shippingArray) && count($this->_shippingArray))
                return $this->_shippingArray;
            else
                return false;
        }
        if (is_array($this->_shippingArray) && array_key_exists($code, $this->_shippingArray))
            return $this->_shippingArray[$code];
        else
            return false;
    }

    public function getLastInventorySyncDate()
    {
        $dateString = $this->getData('inventory_sync_last_date');
        if (!isset($dateString) || empty($dateString)) {
            return false;
        }
        $date = new DateTime($dateString, new DateTimeZone('UTC'));
        return $date->format('c');
    }

    public function setLastInventorySyncDate()
    {
        return $this->setData('inventory_sync_last_date', date("Y-m-d H:i:s", time()))->save();
    }

    public function getLastOrderSyncDate()
    {
        $dateString = $this->getData('orders_sync_last_date');
        if (!isset($dateString) || empty($dateString)) {
            return false;
        }
        $date = new DateTime($dateString, new DateTimeZone('UTC'));
        return $date->format('c');
    }

    public function setLastOrderSyncDate()
    {
        return $this->setData('orders_sync_last_date', date("Y-m-d H:i:s", time()))->save();
    }

    public function duplicate()
    {
        if ($this->getId()) {
            $newMarketplace = Mage::getModel('mws/marketplace');
            $currentData = $this->getData();
            unset($currentData['id']);
            $newMarketplace->setData($currentData)->save();
            foreach ($this->getShippingSettings() as $method)
                $newMethod = Mage::getModel('mws/shipping')->setData($method->getData())->setFbaMarketplaceId($newMarketplace->getId())->save();
            return $newMarketplace;

        }
        return false;
    }

    public function sendAfterPlace() {
       return $this->getSendOrderImmediately() == 1;
    }

    public  function sendAfterInvoice() {
        return $this->getSendOrderImmediately() == 2;
    }

    /**
     * recalculates blocked qty for all marketplace orders
     *
     */
    public function recalculateBlockedQty()
    {
        if ($this->getId()
            && $this->isAmazonInventoryMode()
        ) {
            $ignoreStatuses = array(
                Mage_Sales_Model_Order::STATE_CANCELED,
                Mage_Sales_Model_Order::STATE_CLOSED,
                Mage_Sales_Model_Order::STATE_COMPLETE,
            );

            /** @var Mage_Sales_Model_Resource_Order_Collection $orderCollection */
            $orderCollectionWithQuery = Mage::getModel('sales/order')->getCollection();

            $orderCollectionWithQuery
                ->addFieldToFilter('main_table.fba_marketplace_id', $this->getId());

            $orderCollectionWithQuery->getSelect()
                /** join fba_query table to filter it by status */
                ->joinLeft(
                    array("query" => $orderCollectionWithQuery->getTable('mws/query')),
                    'main_table.fba_query_id = query.id',
                    array(
                        'query_status' => 'status'
                    )
                )
                ->where('query.status <> ' . Webtex_Fba_Model_Mws_Query::STATUS_SUCCESS)
                ->where("main_table.status not in ('" . implode("','", $ignoreStatuses) . "')");

            $orderCollection = Mage::getModel('sales/order')->getCollection();
            $orderCollection
                ->addFieldToFilter('fba_marketplace_id', $this->getId())
                ->addFieldToFilter('fba_query_id', array("null"=>true))
                ->getSelect()
                ->where("main_table.status not in ('" . implode("','", $ignoreStatuses) . "')");

            $orders = $orderCollectionWithQuery->getItems() + $orderCollection->getItems();

            $blockedQty = array();

            foreach ($orders as $order) {
                $orderBlockedQty = array();
                foreach ($order->getAllItems() as $item) {
                    /** @var Mage_Sales_Model_Order_Item $item */
                    if ($item->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
                        $assigned = $this->_getAssigned($item->getProduct());
                        if ($assigned->getFbaMarketplaceId()) {
                            $orderBlockedQty[$item->getProduct()->getSku()] += $item->getQtyOrdered();
                            $blockedQty[$item->getProduct()->getId()]['qty'] += $item->getQtyOrdered();
                            $blockedQty[$item->getProduct()->getId()]['product'] = $item->getProduct();
                        }
                    }
                }

                $order->setBlockedQty(serialize($orderBlockedQty));
                $order->save();
            }

            $affectedIds = array();
            foreach ($blockedQty as $productId => $item) {
                if (array_key_exists($productId, $this->_assignedProducts)) {
                    $assigned = $this->_assignedProducts[$productId];
                    $assigned->setMagentoOrderedQty($item['qty'])
                        ->save();
                    $affectedIds[] = $assigned->getId();
                    /** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
                    $stockItem = Mage::getModel('catalogInventory/stock_item');
                    $stockItem->loadByProduct($item['product']);
                    if (intval($assigned->getQty()) != intval($stockItem->getQty())) {
                        $stockItem->setIsInStock(1);
                        $stockItem->setQty(floatval($assigned->getQty()))->save();
                    }
                }
            }

            if (count($affectedIds)) {
                $resource = Mage::getSingleton('core/resource');
                /** @var Varien_Db_Adapter_Interface $connection */
                $connection = $resource->getConnection('core_write');
                $connection->update(
                    $resource->getTableName('mws/product'),
                    array(
                        'magento_ordered_qty' => '0'
                    ),
                    'id NOT IN (' . implode(',', $affectedIds) . ')'
                );
            }
        }
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return Webtex_Fba_Model_Mws_Product
     */
    private function _getAssigned($product) {
        if (!array_key_exists($product->getId(),$this->_assignedProducts)) {
            $this->_assignedProducts[$product->getId()] = Mage::getModel('mws/product')->loadByProduct($product);
        }

        return $this->_assignedProducts[$product->getId()];
    }
    
    /**
     * Add Value Sort To Collection Select
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @param string $dir direction
     * @return Mage_Eav_Model_Entity_Attribute_Source_Abstract
     */
    public function addValueSortToCollection($collection, $dir = 'asc')
    {
        $attributeCode  = $this->getAttribute()->getAttributeCode();
        $attributeId    = $this->getAttribute()->getId();
        $attributeTable = $this->getAttribute()->getBackend()->getTable();

        if ($this->getAttribute()->isScopeGlobal()) {
            $tableName = $attributeCode . '_t';
            $collection->getSelect()
                ->joinLeft(
                    array($tableName => $attributeTable),
                    "e.entity_id={$tableName}.entity_id"
                        . " AND {$tableName}.attribute_id='{$attributeId}'"
                        . " AND {$tableName}.store_id='0'",
                    array());
            $valueExpr = $tableName . '.value';
        }
        else {
            $valueTable1 = $attributeCode . '_t1';
            $valueTable2 = $attributeCode . '_t2';
            $collection->getSelect()
                ->joinLeft(
                    array($valueTable1 => $attributeTable),
                    "e.entity_id={$valueTable1}.entity_id"
                        . " AND {$valueTable1}.attribute_id='{$attributeId}'"
                        . " AND {$valueTable1}.store_id='0'",
                    array())
                ->joinLeft(
                    array($valueTable2 => $attributeTable),
                    "e.entity_id={$valueTable2}.entity_id"
                        . " AND {$valueTable2}.attribute_id='{$attributeId}'"
                        . " AND {$valueTable2}.store_id='{$collection->getStoreId()}'",
                    array()
                );

                $valueExpr = $collection->getConnection()->getCheckSql(
                    $valueTable2 . '.value_id > 0',
                    $valueTable2 . '.value',
                    $valueTable1 . '.value'
                );
        }

        $collection->getSelect()->order($valueExpr . ' ' . $dir);
        return $this;
    }

}
