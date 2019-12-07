<?php

/**
 * Class Snowdog_SuccessRegistration_Model_Resource_Registration_Collection
 */
class Snowdog_SuccessRegistration_Model_Resource_Registration_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('snowsuccessregistration/registration');
    }
}
