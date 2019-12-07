<?php
/**
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     FBAInboundServiceMWS
 *  @copyright   Copyright 2009 Amazon.com, Inc. All Rights Reserved.
 *  @link        http://mws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2010-10-01
 */
/*******************************************************************************
 *
 *  FBA Inbound Service MWS PHP5 Library
 *  Generated: Fri Oct 22 09:52:55 UTC 2010
 *
 */

/**
 *  @see Mws_FBAInboundServiceMWS_Model
 */
require_once ('Mws/FBAInboundServiceMWS/Model.php');



/**
 * FBAInboundServiceMWS_Model_ListInboundShipmentItemsRequest
 *
 * Properties:
 * <ul>
 *
 * <li>SellerId: string</li>
 * <li>Marketplace: string</li>
 * <li>ShipmentId: string</li>
 * <li>LastUpdatedBefore: string</li>
 * <li>LastUpdatedAfter: string</li>
 *
 * </ul>
 */
class Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsRequest extends Mws_FBAInboundServiceMWS_Model
{


    /**
     * Construct new Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsRequest
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>SellerId: string</li>
     * <li>Marketplace: string</li>
     * <li>ShipmentId: string</li>
     * <li>LastUpdatedBefore: string</li>
     * <li>LastUpdatedAfter: string</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'SellerId' => array('FieldValue' => null, 'FieldType' => 'string'),
        'Marketplace' => array('FieldValue' => null, 'FieldType' => 'string'),
        'ShipmentId' => array('FieldValue' => null, 'FieldType' => 'string'),
        'LastUpdatedBefore' => array('FieldValue' => null, 'FieldType' => 'string'),
        'LastUpdatedAfter' => array('FieldValue' => null, 'FieldType' => 'string'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the SellerId property.
     *
     * @return string SellerId
     */
    public function getSellerId()
    {
        return $this->_fields['SellerId']['FieldValue'];
    }

    /**
     * Sets the value of the SellerId property.
     *
     * @param string SellerId
     * @return this instance
     */
    public function setSellerId($value)
    {
        $this->_fields['SellerId']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the SellerId and returns this instance
     *
     * @param string $value SellerId
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsRequest instance
     */
    public function withSellerId($value)
    {
        $this->setSellerId($value);
        return $this;
    }


    /**
     * Checks if SellerId is set
     *
     * @return bool true if SellerId  is set
     */
    public function isSetSellerId()
    {
        return !is_null($this->_fields['SellerId']['FieldValue']);
    }

    /**
     * Gets the value of the Marketplace property.
     *
     * @return string Marketplace
     */
    public function getMarketplace()
    {
        return $this->_fields['Marketplace']['FieldValue'];
    }

    /**
     * Sets the value of the Marketplace property.
     *
     * @param string Marketplace
     * @return this instance
     */
    public function setMarketplace($value)
    {
        $this->_fields['Marketplace']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the Marketplace and returns this instance
     *
     * @param string $value Marketplace
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsRequest instance
     */
    public function withMarketplace($value)
    {
        $this->setMarketplace($value);
        return $this;
    }


    /**
     * Checks if Marketplace is set
     *
     * @return bool true if Marketplace  is set
     */
    public function isSetMarketplace()
    {
        return !is_null($this->_fields['Marketplace']['FieldValue']);
    }

    /**
     * Gets the value of the ShipmentId property.
     *
     * @return string ShipmentId
     */
    public function getShipmentId()
    {
        return $this->_fields['ShipmentId']['FieldValue'];
    }

    /**
     * Sets the value of the ShipmentId property.
     *
     * @param string ShipmentId
     * @return this instance
     */
    public function setShipmentId($value)
    {
        $this->_fields['ShipmentId']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the ShipmentId and returns this instance
     *
     * @param string $value ShipmentId
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsRequest instance
     */
    public function withShipmentId($value)
    {
        $this->setShipmentId($value);
        return $this;
    }


    /**
     * Checks if ShipmentId is set
     *
     * @return bool true if ShipmentId  is set
     */
    public function isSetShipmentId()
    {
        return !is_null($this->_fields['ShipmentId']['FieldValue']);
    }

    /**
     * Gets the value of the LastUpdatedBefore property.
     *
     * @return string LastUpdatedBefore
     */
    public function getLastUpdatedBefore()
    {
        return $this->_fields['LastUpdatedBefore']['FieldValue'];
    }

    /**
     * Sets the value of the LastUpdatedBefore property.
     *
     * @param string LastUpdatedBefore
     * @return this instance
     */
    public function setLastUpdatedBefore($value)
    {
        $this->_fields['LastUpdatedBefore']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the LastUpdatedBefore and returns this instance
     *
     * @param string $value LastUpdatedBefore
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsRequest instance
     */
    public function withLastUpdatedBefore($value)
    {
        $this->setLastUpdatedBefore($value);
        return $this;
    }


    /**
     * Checks if LastUpdatedBefore is set
     *
     * @return bool true if LastUpdatedBefore  is set
     */
    public function isSetLastUpdatedBefore()
    {
        return !is_null($this->_fields['LastUpdatedBefore']['FieldValue']);
    }

    /**
     * Gets the value of the LastUpdatedAfter property.
     *
     * @return string LastUpdatedAfter
     */
    public function getLastUpdatedAfter()
    {
        return $this->_fields['LastUpdatedAfter']['FieldValue'];
    }

    /**
     * Sets the value of the LastUpdatedAfter property.
     *
     * @param string LastUpdatedAfter
     * @return this instance
     */
    public function setLastUpdatedAfter($value)
    {
        $this->_fields['LastUpdatedAfter']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the LastUpdatedAfter and returns this instance
     *
     * @param string $value LastUpdatedAfter
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentItemsRequest instance
     */
    public function withLastUpdatedAfter($value)
    {
        $this->setLastUpdatedAfter($value);
        return $this;
    }


    /**
     * Checks if LastUpdatedAfter is set
     *
     * @return bool true if LastUpdatedAfter  is set
     */
    public function isSetLastUpdatedAfter()
    {
        return !is_null($this->_fields['LastUpdatedAfter']['FieldValue']);
    }




}
