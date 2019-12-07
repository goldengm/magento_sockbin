<?php

/**
 * Class Snowdog_SuccessRegistration_Model_Resource_Registration
 */
class Snowdog_SuccessRegistration_Model_Resource_Registration
    extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('snowsuccessregistration/registration', 'entity_id');
    }

    /**
     * Get orders by a given customer email
     *
     * @param string $customerEmail Customer email
     *
     * @return array
     */
    public function loadOrdersByCustomerEmail($customerEmail)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('customer_email = ?', $customerEmail);

        return $adapter->fetchRow($select);
    }
}