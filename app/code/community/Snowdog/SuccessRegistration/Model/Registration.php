<?php

/**
 * Class Snowdog_SuccessRegistration_Model_Registration
 *
 * @method string getCustomerEmail()
 * @method Snowdog_SuccessRegistration_Model_Registration setCustomerEmail(string $value)
 * @method int getOrderId()
 * @method Snowdog_SuccessRegistration_Model_Registration setOrderId(int $value)
 */
class Snowdog_SuccessRegistration_Model_Registration
    extends Mage_Core_Model_Abstract
{

    /**
     * 
     */
    protected function _construct()
    {
        $this->_init('snowsuccessregistration/registration');
    }

}