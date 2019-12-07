<?php

/**
 * Class Snowdog_SuccessRegistration_Helper_Data
 */
class Snowdog_SuccessRegistration_Helper_Data
    extends Mage_Core_Helper_Abstract
{

    /**
     * Get success registration form url action
     *
     * @return string
     */
    public function getSuccessRegistrationFormUrl()
    {
        return Mage::getUrl('successregistration/index/registerPost');
    }

}