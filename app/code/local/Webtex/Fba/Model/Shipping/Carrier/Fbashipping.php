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

class Webtex_Fba_Model_Shipping_Carrier_Fbashipping
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    const CODE = 'fbashipping';
    protected $_code = self::CODE;
    protected $_isFixed = true;
    protected $_currentCode = "";
    /** @var \Webtex_Fba_Model_Mws_Marketplace|null */
    protected $_currentMarketplace = null;
    protected $_currentMarketplaceId = null;

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {

        $allWeight = 0;
        $freeBoxes = 0;
        if ($items = $request->getAllItems()) {
            foreach ($items as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren()) {
                    foreach ($item->getChildren() as $child) {
                        /** @var $product Mage_Catalog_Model_Product */
                        $product = $child->getProduct();
                        /** @var $assigned Webtex_Fba_Model_Mws_Product */
                        $assigned = Mage::getModel('mws/product')->loadByProduct($product);

                        if ($assigned->getId()) {
                            $this->_currentMarketplaceId = $assigned->getFbaMarketplaceId();
                            $weight = $child->getWeight();
                            //one page checkout fix
                            if (!$weight)
                                $weight = Mage::helper('fba')->getProductWeight($child->getProduct()->getEntityId(), $child->getStoreId());
                            $allWeight += $item->getQty() * $weight * $child->getQty();
                            if ($child->getFreeShipping() && !$child->getProduct()->isVirtual())
                                $freeBoxes += $item->getQty() * $child->getQty();
                        }
                    }
                } else {
                    /** @var $product Mage_Catalog_Model_Product */
                    $product = $item->getProduct();
                    /** @var $assigned Webtex_Fba_Model_Mws_Product */
                    $assigned = Mage::getModel('mws/product')->loadByProduct($product);
                    if ($assigned->getId()) {
                        $this->_currentMarketplaceId = $assigned->getFbaMarketplaceId();

                        $weight = $item->getWeight();
                        //one page checkout fix
                        if (!$weight)
                            $weight = Mage::helper('fba')->getProductWeight($item->getProduct()->getEntityId(), $item->getStoreId());
                        $allWeight += $weight * $item->getQty();
                        if ($item->getFreeShipping())
                            $freeBoxes += $item->getQty();
                    }
                }
            }
        }
        $this->setFreeBoxes($freeBoxes);

        if ($allWeight > 0) {
            $this->setAllWeight($allWeight);
            $result = Mage::getModel('shipping/rate_result');

            foreach (Mage::getModel('fba/config_source_shippingType')->getMethods() as $methodSource) {
                $this->_currentCode = $methodSource['code'];
                if ($this->getConfigData('is_active') && $this->_checkAvailableShipCountriesForMethod($request)) {
                    $shippingPrice = $this->getShippingPrice($this->_currentCode);
                    if ($shippingPrice !== false) {
                        $method = Mage::getModel('shipping/rate_result_method');
                        $method->setCarrier('fbashipping');
                        $method->setCarrierTitle($this->getCarrierTitle($request->getStoreId()));

                        $method->setMethod($methodSource['name']);
                        $method->setMethodTitle(trim($this->getConfigData('title')) != "" ? trim($this->getConfigData('title')) : ucfirst($methodSource['name']));

                        if ($this->getConfigData('is_free') && ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()))
                            $shippingPrice = '0.00';


                        $method->setPrice($shippingPrice);
                        $method->setCost($shippingPrice);

                        $result->append($method);
                    }
                }
            }

            return $result;
        } else
            return false;
    }

    private function _checkAvailableShipCountriesForMethod(Mage_Shipping_Model_Rate_Request $request)
    {
        $speCountriesAllow = $this->getConfigData('allow_specific_country');
        if ($speCountriesAllow === -1)
            return true;
        /*
        * for specific countries, the flag will be 1
        */
        if ($speCountriesAllow && $speCountriesAllow == 1) {
            $availableCountries = array();
            if ($this->getConfigData('country')) {
                $availableCountries = explode(',', $this->getConfigData('country'));
            }
            if ($availableCountries && in_array($request->getDestCountryId(), $availableCountries)) {
                return true;
            } elseif (!$availableCountries || ($availableCountries
                && !in_array($request->getDestCountryId(), $availableCountries))
            )
                return false; else
                /*
               * The admin set not to show the shipping module if the devliery country is not within specific countries
               */
                return false;
        }
        return true;
    }

    public function getAllowedMethods()
    {
        return Mage::getModel('fba/config_source_shippingType')->getAllowedMethods();
    }

    public function getShippingPrice()
    {
        if ($this->_currentMarketplace === null || !$this->_currentMarketplace->getId())
            $this->_currentMarketplace = Mage::getModel('mws/martketplace')->load($this->_currentMarketplaceId);

        if ($this->_currentCode === null || !$this->_currentMarketplace->getId()) {
            return false;
        }
        $shippingMethod = $this->_currentMarketplace->getShippingSettings($this->_currentCode);
        if ($shippingMethod->getId()) {
            $priceRules = $shippingMethod->getRules();
            if (is_array($priceRules)
                && isset($priceRules['from'])
                && isset($priceRules['to'])
                && isset($priceRules['price'])
            ) {
                $from = $priceRules['from'];
                $to = $priceRules['to'];
                $price = $priceRules['price'];

                foreach ($from as $key => $value) {
                    if ($this->getAllWeight() >= ($from[$key] * 1.0) && $this->getAllWeight() <= ($to[$key] * 1.0)) {
                        return $price[$key];
                    }
                }
            }
        }

        return false;
    }

    /**
     * Retrieve information from carrier configuration
     *
     * @param   string $field
     * @return  mixed
     */
    public function getConfigData($field)
    {

        if ($this->_currentMarketplace === null || !$this->_currentMarketplace->getId())
            $this->_currentMarketplace = Mage::getModel('mws/marketplace')->load($this->_currentMarketplaceId);

        if ($this->_currentCode === null || !$this->_currentMarketplace->getId()) {
            return -1;
        }
        $shippingMethod = $this->_currentMarketplace->getShippingSettings($this->_currentCode);
        if ($shippingMethod && $shippingMethod->getId())
            return $shippingMethod->getData($field);
        else
            return false;
    }

    private function getCarrierTitle($storeId)
    {
         return Mage::getStoreConfig('carriers/fbashipping/title', $storeId);
    }
}
