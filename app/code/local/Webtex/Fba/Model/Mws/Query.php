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
 *  - class - class name
 *  - method - method name
 *  - create_date - query creation date
 *  - execution_time - query execution time
 *  - last_execution_date - date of last query execution
 *  - status - status of last query execution
 *  - error_message - error message
 *  - request - encrypted request data
 *  - response - encrypted response data
 *  - input_data - serialized input data
 *  - parent_id - parent query primary key | 0 if no parent
 *  - priority - query priority | 0 for immediately query
 *  - locked - true if query unique
 *  - marketplace_id - amazon marketplace id
 *
 * methods:
 * @method int getId()
 * @method Webtex_Fba_Model_Mws_Query setClass(string)
 * @method string getClass()
 * @method Webtex_Fba_Model_Mws_Query setMethod(string)
 * @method string getMethod()
 * @method Webtex_Fba_Model_Mws_Query setInputData(string)
 * @method string getInputData()
 * @method Webtex_Fba_Model_Mws_Query setCreateDate(string)
 * @method string getCreateDate()
 * @method Webtex_Fba_Model_Mws_Query setExecutionTime(float)
 * @method float getExecutionTime()
 * @method Webtex_Fba_Model_Mws_Query setLastExecutionDate(string)
 * @method string getLastExecutionDate()
 * @method Webtex_Fba_Model_Mws_Query setStatus(int)
 * @method int getStatus()
 * @method Webtex_Fba_Model_Mws_Query setErrorMessage(string)
 * @method string getErrorMessage()
 * @method Webtex_Fba_Model_Mws_Query setRequest(string)
 * @method string getRequest()
 * @method Webtex_Fba_Model_Mws_Query setResponse(string)
 * @method string getResponse()
 * @method Webtex_Fba_Model_Mws_Query setExecutionResult(string)
 * @method string getExecutionResult()
 * @method Webtex_Fba_Model_Mws_Query setRequired(boolean)
 * @method boolean getRequired()
 * @method Webtex_Fba_Model_Mws_Query setRequestId(string)
 * @method string getRequestId()
 * @method Webtex_Fba_Model_Mws_Query setParentId(int)
 * @method int getParentId()
 * @method Webtex_Fba_Model_Mws_Query setPriority(int)
 * @method int getPriority()
 * @method Webtex_Fba_Model_Mws_Query setFbaMarketplaceId(int)
 * @method int getFbaMarketplaceId()
 * @method Webtex_Fba_Model_Mws_Query setLocked(bool)
 * @method bool getLocked()
 * @method Webtex_Fba_Model_Mws_Query setChildQueries(array)
 * @method Webtex_Fba_Model_Mws_Query[] getChildQueries(array)
 */
class Webtex_Fba_Model_Mws_Query extends Mage_Core_Model_Abstract
{
    const STATUS_FAULT = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_THROTTLED = 2;
    const RETRY_COUNT = 10;

    private $_plainData = null;

    private $_plainRequest = '';

    private $_plainResponse = '';

    public function _construct()
    {
        parent::_construct();
        $this->_init('mws/query');
    }

    /**
     * get plain data (decrypt and unserialize if necessary)
     *
     * @return array|null
     */
    public function getPlainData()
    {
        if ($this->_plainData === null && $this->getInputData())
            $this->_plainData = unserialize(Mage::getModel('core/encryption')->decrypt($this->getInputData()));
        return $this->_plainData;
    }

    /**
     * set query plain data
     *
     * @param array $data
     */
    public function setPlainData($data)
    {
        $this->_plainData = $data;
        return $this;
    }

    /**
     * get plain request (decrypt if necessary)
     *
     * @return string
     */
    public function getPlainRequest()
    {
        if (empty($this->_plainRequest) && $this->getRequest())
            $this->_plainRequest = Mage::getModel('core/encryption')->decrypt($this->getRequest());
        return $this->_plainRequest;
    }

    /**
     * set query plain request
     *
     * @param string $xml
     */
    public function setPlainRequest($xml)
    {
        $this->_plainRequest = $xml;
    }

    /**
     * get plain response (decrypt if necessary)
     *
     * @return string
     */
    public function getPlainResponse()
    {
        if (empty($this->_plainResponse) && $this->getResponse())
            $this->_plainResponse = Mage::getModel('core/encryption')->decrypt($this->getResponse());
        return $this->_plainResponse;
    }

    /**
     * set query plain response
     *
     * @param string $xml
     */
    public function setPlainResponse($xml)
    {
        $this->_plainResponse = $xml;
    }

    /**
     * Processing object before save data
     *
     * @return Webtex_Fba_Model_Mws_Query
     */
    protected function _beforeSave()
    {
        if (!$this->getCreateDate())
            $this->setCreateDate(date("Y-m-d H:i:s", time()));
        if ($this->_plainData && is_array($this->_plainData)) {
            $this->setInputData(Mage::getModel('core/encryption')->encrypt(serialize($this->prepareDataArray($this->_plainData))));
        }
        if (!empty($this->_plainRequest))
            $this->setRequest(Mage::getModel('core/encryption')->encrypt($this->_plainRequest));
        if (!empty($this->_plainResponse))
            $this->setResponse(Mage::getModel('core/encryption')->encrypt($this->_plainResponse));
        return parent::_beforeSave();
    }

    protected function prepareDataArray($data)
    {
        $result = array();
        foreach ($data as $key => $item)
            if (!is_object($item) && !is_array($item))
                $result[$key] = $item;
            elseif (is_array($item))
                $result[$key] = $this->prepareDataArray($item);
        return $result;
    }

    /**
     * execute query and postprocessing results
     *
     * @return Webtex_Fba_Model_Mws_Query
     */
    public function execute()
    {
        /** @var $timer Webtex_Fba_Model_Mws_Timer */
        $timer = Mage::getModel('mws/Timer');
        $this->setLastExecutionDate(date("Y-m-d H:i:s", time()));

        $childQueries = array();

        $queryObject = Mage::getModel('mws/' . $this->getClass(), $this->getFbaMarketplaceId());
        if ($queryObject && method_exists($queryObject, $this->getMethod()) && $this->getStatus() != self::STATUS_SUCCESS) {
            $result = call_user_func(array($queryObject, $this->getMethod()), $this->getPlainData());
            if (isset($result['request_id']))
                $this->setRequestId($result['request_id']);
            if ($result['code'] == 1)
                $this->setStatus(self::STATUS_SUCCESS);
            else {
                if (isset($result['exception'])
                    && is_object($result['exception'])
                    && method_exists($result['exception'], 'getStatusCode')
                    && $result['exception']->getStatusCode() == 503
                )
                    $this->setStatus(self::STATUS_THROTTLED);
                else
                    $this->setStatus(self::STATUS_FAULT);

                if (isset($result['message']))
                    $this->setErrorMessage($result['message']);
            }
            if (isset($result['request']))
                $this->setPlainRequest($result['request']);
            if (isset($result['response']))
                $this->setPlainResponse($result['response']);

            if (isset($result['child_queries']))
                foreach ($result['child_queries'] as $childQuery)
                    /** @var $childQuery Webtex_Fba_Model_Mws_Query */
                    $childQueries[] = $childQuery->setParentId($this->getId())
                        ->setPriority($this->getPriority())
                        ->setCreateDate($this->getCreateDate())
                        ->setFbaMarketplaceId($this->getFbaMarketplaceId())
                        ->setStatus(self::STATUS_THROTTLED)
                        ->save();
        } else {
            $this->setStatus(self::STATUS_FAULT)
                ->setErrorMessage('Bad model or method name');
            $result['code'] = 0;
            $result['message'] = 'Bad model or method name';
        }
        return $this->setExecutionResult($result)
            ->setExecutionTime($timer->getLapTiming())
            ->setChildQueries($childQueries)
            ->save();
    }

    /**
     * get all classes names from query table
     *
     * @return array
     */
    static public function getClassesOptions()
    {
        $result = array();
        /** @var $collection Webtex_Fba_Model_Mws_Resource_Query_Collection */
        $collection = Mage::getModel('mws/query')->getCollection();
        $collection->addExpressionFieldToSelect('id_count', 'COUNT(id)', array('id'))->getSelect()->group('class');
        foreach ($collection as $item)
            $result[$item->getClass()] = $item->getClass();
        return $result;
    }

    /**
     * get all classes names from query table
     *
     * @return array
     */
    static public function getQueueClassesOptions()
    {
        $result = array();
        /** @var $collection Webtex_Fba_Model_Mws_Resource_Query_Collection */
        $collection = Mage::getModel('mws/query')->getCollection()->addFieldToFilter('priority', array('neq' => 0))
            ->addFieldToFilter('status', Webtex_Fba_Model_Mws_Query::STATUS_THROTTLED)
            ->addFieldToFilter('fba_marketplace_id', array('neq' => 0));
        $collection->addExpressionFieldToSelect('id_count', 'COUNT(id)', array('id'))->getSelect()->group('class');
        foreach ($collection as $item)
            $result[$item->getClass()] = $item->getClass();
        return $result;
    }

    /**
     * get all methods names from query table
     *
     * @return array
     */
    static public function getMethodsOptions()
    {
        $filter = Mage::app()->getFrontController()->getRequest()->getParam('filter');
        if (is_string($filter)) {
            $data = Mage::helper('adminhtml')->prepareFilterString($filter);
        }
        $result = array();
        /** @var $collection Webtex_Fba_Model_Mws_Resource_Query_Collection */
        $collection = Mage::getModel('mws/query')->getCollection();
        if (!empty($data['class']))
            $collection->addFieldToFilter('class', $data['class']);
        $collection->addExpressionFieldToSelect('id_count', 'COUNT(id)', array('id'))->getSelect()->group('method');
        foreach ($collection as $item)
            $result[$item->getMethod()] = $item->getMethod();
        if (!empty($data['method']) && !in_array($data['method'], $result)) {
            unset($data['method']);
            Mage::app()->getFrontController()->getRequest()->setParam('filter', $data);
        }
        return $result;
    }

    /**
     * get all methods names from query table
     *
     * @return array
     */
    static public function getQueueMethodsOptions()
    {
        $filter = Mage::app()->getFrontController()->getRequest()->getParam('filter');
        if (is_string($filter)) {
            $data = Mage::helper('adminhtml')->prepareFilterString($filter);
        }
        $result = array();
        /** @var $collection Webtex_Fba_Model_Mws_Resource_Query_Collection */
        $collection = Mage::getModel('mws/query')->getCollection()->addFieldToFilter('priority', array('neq' => 0))
            ->addFieldToFilter('status', Webtex_Fba_Model_Mws_Query::STATUS_THROTTLED)
            ->addFieldToFilter('fba_marketplace_id', array('neq' => 0));
        if (!empty($data['class']))
            $collection->addFieldToFilter('class', $data['class']);
        $collection->addExpressionFieldToSelect('id_count', 'COUNT(id)', array('id'))->getSelect()->group('method');
        foreach ($collection as $item)
            $result[$item->getMethod()] = $item->getMethod();
        if (!empty($data['method']) && !in_array($data['method'], $result)) {
            unset($data['method']);
            Mage::app()->getFrontController()->getRequest()->setParam('filter', $data);
        }
        return $result;
    }

    /**
     *
     * @return array
     */
    static public function getStatusOptions()
    {
        return array(
            self::STATUS_SUCCESS => 'Success',
            self::STATUS_FAULT => 'Fault',
            self::STATUS_THROTTLED => 'Throttled / Not Send',
        );
    }

    /**
     * @param int $priority
     * @return Webtex_Fba_Model_Mws_Query
     */
    public function postpone($priority = 2)
    {
        return $this->setStatus(self::STATUS_THROTTLED)
            ->setPriority((int)$priority)
            ->save();
    }
}