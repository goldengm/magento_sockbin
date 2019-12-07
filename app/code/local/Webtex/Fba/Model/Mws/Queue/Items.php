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

class Webtex_Fba_Model_Mws_Queue_Items
{
    protected $_items = array();

    protected $_currentPosition = 0;

    /**
     * init queue and get items from DB
     */
    public function __construct(Webtex_Fba_Model_Mws_Marketplace $marketplace)
    {
        /** @var $collection Webtex_Fba_Model_Mws_Resource_Query_Collection */
        $collection = Mage::getModel('mws/query')->getCollection();
        $collection->addFieldToFilter('priority', array('neq' => 0))
            ->addFieldToFilter('status', Webtex_Fba_Model_Mws_Query::STATUS_THROTTLED)
            ->addFieldToFilter('fba_marketplace_id', $marketplace->getId())
            ->addOrder('priority', Varien_Data_Collection::SORT_ORDER_ASC)
            ->addOrder('create_date', Varien_Data_Collection::SORT_ORDER_ASC);
        $this->_items = array_values($collection->getItems());
    }

    /**
     * return current item of queue
     *
     * @return bool|Webtex_Fba_Model_Mws_Query
     */
    public function getCurrent()
    {
        if ($this->isCurrentExist())
            return $this->_items[$this->_currentPosition];
        else
            return false;
    }

    /**
     * go to next item and return it
     *
     * @return bool|Webtex_Fba_Model_Mws_Query
     */
    public function goNext()
    {
        ++$this->_currentPosition;
        return $this->getCurrent();
    }

    /**
     * go to first item and return it
     *
     * @return bool|Webtex_Fba_Model_Mws_Query
     */
    public function goFirst()
    {
        $this->_currentPosition = 0;
        return $this->getCurrent();
    }

    /**
     * insert new items after current item and return true for success
     *
     * @param Webtex_Fba_Model_Mws_Query[] $items
     * @return bool
     */
    public function insertAfterCurrent($items = array())
    {
        if ($this->isCurrentExist() && is_array($items)) {
            $head = array_slice($this->_items, 0, $this->_currentPosition + 1);
            $tail = array_slice($this->_items, $this->_currentPosition + 1);
            $this->_items = $head;
            foreach ($items as $newItem)
                if ($newItem instanceof Webtex_Fba_Model_Mws_Query)
                    $this->_items[] = $newItem;
            foreach ($tail as $newItem)
                $this->_items[] = $newItem;
            return true;
        } else
            return false;
    }

    /**
     * insert new items in end of queue and return true for success
     *
     * @param Webtex_Fba_Model_Mws_Query[] $items
     * @return bool
     */
    public function addItems($items = array())
    {
        if (is_array($items)) {
            foreach ($items as $newItem)
                if ($newItem instanceof Webtex_Fba_Model_Mws_Query)
                    $this->_items[] = $newItem;
            return true;
        } else
            return false;
    }

    /**
     * @return bool
     */
    public function isCurrentExist()
    {
        return array_key_exists($this->_currentPosition, $this->_items);
    }
}