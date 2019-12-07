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

class Webtex_Fba_Model_Mws_Queue
{
    /**
     * process id for lock process
     */
    const PROCESS_ID = 'amazon_queue';

    /**
     * Mage_Index_Model_Process will provide lock file support
     *
     * @var Mage_Index_Model_Process $_lockProcess
     */
    private $_lockProcess;


    public function __construct()
    {
        $this->_lockProcess = new Mage_Index_Model_Process();
        $this->_lockProcess->setId(self::PROCESS_ID);
    }

    /**
     * get current queue items
     *
     * @return Webtex_Fba_Model_Mws_Queue_Items
     */
    public function _getItems()
    {
        $items = array();

        /** @var $marketplaces Webtex_Fba_Model_Mws_Resource_Marketplace_Collection */
        $marketplaces = Mage::getModel('mws/marketplace')->getCollection();
        $marketplaces->addFieldToFilter('status', 1);
        foreach ($marketplaces as $marketplace)
            $items[] = Mage::getModel('mws/queue_items', $marketplace);

        return $items;
    }

    public function processQueue()
    {

        Mage::log('Amazon queue processing started', null, 'webtex-fba-queue.log');

        if ($this->_lockProcess->isLocked()) {
            Mage::log('Another Amazon queue processing instance is running!', null, 'webtex-fba-queue.log');
            return false;
        }

        $this->_lockProcess->lockAndBlock();
        try {

            /** @var $queues Webtex_Fba_Model_Mws_Queue_Items[] */
            $queues = $this->_getItems();
            foreach ($queues as $queue) {
                while ($queue->isCurrentExist()) {
                    $queue->getCurrent()->execute();
                    if ($queue->getCurrent()->getStatus() == Webtex_Fba_Model_Mws_Query::STATUS_THROTTLED)
                        break;
                    $childQueries = $queue->getCurrent()->getChildQueries();
                    if (is_array($childQueries) && count($childQueries))
                        $queue->insertAfterCurrent($childQueries);
                    $queue->goNext();
                }
            }
        } catch(Exception $e) {
            Mage::log($e->getMessage(), null, 'webtex-fba-queue.log');
        }

        $this->_lockProcess->unlock();
        return true;
    }

    public function isQueueLocked() {
        return $this->_lockProcess->isLocked();
    }

    public function forceUnlock(){
        $this->_lockProcess->unlock();
    }
}