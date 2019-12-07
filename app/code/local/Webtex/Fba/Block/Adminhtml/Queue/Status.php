<?php

class Webtex_Fba_Block_Adminhtml_Queue_Status extends Mage_Adminhtml_Block_Abstract {

    public function isQueueLocked()
    {
        /** @var Webtex_Fba_Model_Mws_Queue $queue */
        $queue = Mage::getModel('mws/queue');
        return $queue->isQueueLocked();
    }

    public function getLastExecutionTime()
    {
        /** @var Mage_Cron_Model_Schedule $cronJob */
        $cronJob = Mage::getModel('cron/schedule')->getCollection()->addFieldToFilter('job_code','send_queue')
            ->addFieldToFilter('finished_at',array('notnull'=>true))->setOrder('finished_at',Varien_Data_Collection::SORT_ORDER_DESC)->getFirstItem();
        if($cronJob)
            return $cronJob->getFinishedAt();
        else
            return "";
    }

    protected function _prepareLayout()
    {
        $syncButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id' => 'force_reset_lock',
                'label' => Mage::helper('fba')->__('Force reset lock file'),
                'class' => 'save',
                'on_click' => 'setLocation(\'' . $this->getResetQueueUrl() . '\')'
            ));
        $this->setChild('force_reset_lock', $syncButton);
        return parent::_prepareLayout();
    }

    public function getResetQueueUrl()
    {
        return $this->getUrl('fba/adminhtml_index/resetQueue');
    }
}