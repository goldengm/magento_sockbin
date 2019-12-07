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
 * FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest
 *
 * Properties:
 * <ul>
 *
 * <li>SellerId: string</li>
 * <li>Marketplace: string</li>
 * <li>ShipmentId: string</li>
 * <li>InboundShipmentHeader: FBAInboundServiceMWS_Model_InboundShipmentHeader</li>
 * <li>InboundShipmentItems: FBAInboundServiceMWS_Model_InboundShipmentItemList</li>
 *
 * </ul>
 */
class Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest extends Mws_FBAInboundServiceMWS_Model
{


    /**
     * Construct new Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>SellerId: string</li>
     * <li>Marketplace: string</li>
     * <li>ShipmentId: string</li>
     * <li>InboundShipmentHeader: FBAInboundServiceMWS_Model_InboundShipmentHeader</li>
     * <li>InboundShipmentItems: FBAInboundServiceMWS_Model_InboundShipmentItemList</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'SellerId' => array('FieldValue' => null, 'FieldType' => 'string'),
        'Marketplace' => array('FieldValue' => null, 'FieldType' => 'string'),
        'ShipmentId' => array('FieldValue' => null, 'FieldType' => 'string'),
        'InboundShipmentHeader' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAInboundServiceMWS_Model_InboundShipmentHeader'),
        'InboundShipmentItems' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAInboundServiceMWS_Model_InboundShipmentItemList'),
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
     * @return Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest instance
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
     * @return Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest instance
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
     * @return Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest instance
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
     * Gets the value of the InboundShipmentHeader.
     *
     * @return InboundShipmentHeader InboundShipmentHeader
     */
    public function getInboundShipmentHeader()
    {
        return $this->_fields['InboundShipmentHeader']['FieldValue'];
    }

    /**
     * Sets the value of the InboundShipmentHeader.
     *
     * @param InboundShipmentHeader InboundShipmentHeader
     * @return void
     */
    public function setInboundShipmentHeader($value)
    {
        $this->_fields['InboundShipmentHeader']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the InboundShipmentHeader  and returns this instance
     *
     * @param InboundShipmentHeader $value InboundShipmentHeader
     * @return Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest instance
     */
    public function withInboundShipmentHeader($value)
    {
        $this->setInboundShipmentHeader($value);
        return $this;
    }


    /**
     * Checks if InboundShipmentHeader  is set
     *
     * @return bool true if InboundShipmentHeader property is set
     */
    public function isSetInboundShipmentHeader()
    {
        return !is_null($this->_fields['InboundShipmentHeader']['FieldValue']);

    }

    /**
     * Gets the value of the InboundShipmentItems.
     *
     * @return InboundShipmentItemList InboundShipmentItems
     */
    public function getInboundShipmentItems()
    {
        return $this->_fields['InboundShipmentItems']['FieldValue'];
    }

    /**
     * Sets the value of the InboundShipmentItems.
     *
     * @param InboundShipmentItemList InboundShipmentItems
     * @return void
     */
    public function setInboundShipmentItems($value)
    {
        $this->_fields['InboundShipmentItems']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the InboundShipmentItems  and returns this instance
     *
     * @param InboundShipmentItemList $value InboundShipmentItems
     * @return Mws_FBAInboundServiceMWS_Model_UpdateInboundShipmentRequest instance
     */
    public function withInboundShipmentItems($value)
    {
        $this->setInboundShipmentItems($value);
        return $this;
    }


    /**
     * Checks if InboundShipmentItems  is set
     *
     * @return bool true if InboundShipmentItems property is set
     */
    public function isSetInboundShipmentItems()
    {
        return !is_null($this->_fields['InboundShipmentItems']['FieldValue']);

    }




}
