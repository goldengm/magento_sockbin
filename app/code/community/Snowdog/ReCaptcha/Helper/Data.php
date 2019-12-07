<?php

/**
 * Class Snowdog_ReCaptcha_Helper_Data
 */
class Snowdog_ReCaptcha_Helper_Data
    extends Mage_Core_Helper_Abstract
{

    const API_KEY_CONFIG    = 'snowrecaptcha/general/api_key';
    const SECRET_KEY_CONFIG = 'snowrecaptcha/general/secret_key';

    /**
     * Return reCaptcha api key
     *
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->getConfig(self::API_KEY_CONFIG);
    }

    /**
     * Return reCaptcha secret key
     *
     * @return mixed
     */
    public function getSecretKey()
    {
        return $this->getConfig(self::SECRET_KEY_CONFIG);
    }

    /**
     * Return configuration from a path
     *
     * @param $path
     *
     * @return mixed
     */
    public function getConfig($path)
    {
        return Mage::getStoreConfig($path);
    }

}