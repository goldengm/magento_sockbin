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
 * FBAInboundServiceMWS_Model_InboundShipmentItem
 *
 * Properties:
 * <ul>
 *
 * <li>ShipmentId: string</li>
 * <li>SellerSKU: string</li>
 * <li>FulfillmentNetworkSKU: string</li>
 * <li>QuantityShipped: int</li>
 * <li>QuantityReceived: int</li>
 * <li>QuantityInCase: int</li>
 *
 * </ul>
 */
class Mws_FBAInboundServiceMWS_Model_InboundShipmentItem extends Mws_FBAInboundServiceMWS_Model
{

    /**
     * Construct new Mws_FBAInboundServiceMWS_Model_InboundShipmentItem
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>ShipmentId: string</li>
     * <li>SellerSKU: string</li>
     * <li>FulfillmentNetworkSKU: string</li>
     * <li>QuantityShipped: int</li>
     * <li>QuantityReceived: int</li>
     * <li>QuantityInCase: int</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'ShipmentId' => array('FieldValue' => null, 'FieldType' => 'string'),
        'SellerSKU' => array('FieldValue' => null, 'FieldType' => 'string'),
        'FulfillmentNetworkSKU' => array('FieldValue' => null, 'FieldType' => 'string'),
        'QuantityShipped' => array('FieldValue' => null, 'FieldType' => 'int'),
        'QuantityReceived' => array('FieldValue' => null, 'FieldType' => 'int'),
        'QuantityInCase' => array('FieldValue' => null, 'FieldType' => 'int'),
        );
        parent::__construct($data);
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
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentItem instance
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
     * Gets the value of the SellerSKU property.
     *
     * @return string SellerSKU
     */
    public function getSellerSKU()
    {
        return $this->_fields['SellerSKU']['FieldValue'];
    }

    /**
     * Sets the value of the SellerSKU property.
     *
     * @param string SellerSKU
     * @return this instance
     */
    public function setSellerSKU($value)
    {
        $this->_fields['SellerSKU']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the SellerSKU and returns this instance
     *
     * @param string $value SellerSKU
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentItem instance
     */
    public function withSellerSKU($value)
    {
        $this->setSellerSKU($value);
        return $this;
    }


    /**
     * Checks if SellerSKU is set
     *
     * @return bool true if SellerSKU  is set
     */
    public function isSetSellerSKU()
    {
        return !is_null($this->_fields['SellerSKU']['FieldValue']);
    }

    /**
     * Gets the value of the FulfillmentNetworkSKU property.
     *
     * @return string FulfillmentNetworkSKU
     */
    public function getFulfillmentNetworkSKU()
    {
        return $this->_fields['FulfillmentNetworkSKU']['FieldValue'];
    }

    /**
     * Sets the value of the FulfillmentNetworkSKU property.
     *
     * @param string FulfillmentNetworkSKU
     * @return this instance
     */
    public function setFulfillmentNetworkSKU($value)
    {
        $this->_fields['FulfillmentNetworkSKU']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the FulfillmentNetworkSKU and returns this instance
     *
     * @param string $value FulfillmentNetworkSKU
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentItem instance
     */
    public function withFulfillmentNetworkSKU($value)
    {
        $this->setFulfillmentNetworkSKU($value);
        return $this;
    }


    /**
     * Checks if FulfillmentNetworkSKU is set
     *
     * @return bool true if FulfillmentNetworkSKU  is set
     */
    public function isSetFulfillmentNetworkSKU()
    {
        return !is_null($this->_fields['FulfillmentNetworkSKU']['FieldValue']);
    }

    /**
     * Gets the value of the QuantityShipped property.
     *
     * @return int QuantityShipped
     */
    public function getQuantityShipped()
    {
        return $this->_fields['QuantityShipped']['FieldValue'];
    }

    /**
     * Sets the value of the QuantityShipped property.
     *
     * @param int QuantityShipped
     * @return this instance
     */
    public function setQuantityShipped($value)
    {
        $this->_fields['QuantityShipped']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the QuantityShipped and returns this instance
     *
     * @param int $value QuantityShipped
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentItem instance
     */
    public function withQuantityShipped($value)
    {
        $this->setQuantityShipped($value);
        return $this;
    }


    /**
     * Checks if QuantityShipped is set
     *
     * @return bool true if QuantityShipped  is set
     */
    public function isSetQuantityShipped()
    {
        return !is_null($this->_fields['QuantityShipped']['FieldValue']);
    }

    /**
     * Gets the value of the QuantityReceived property.
     *
     * @return int QuantityReceived
     */
    public function getQuantityReceived()
    {
        return $this->_fields['QuantityReceived']['FieldValue'];
    }

    /**
     * Sets the value of the QuantityReceived property.
     *
     * @param int QuantityReceived
     * @return this instance
     */
    public function setQuantityReceived($value)
    {
        $this->_fields['QuantityReceived']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the QuantityReceived and returns this instance
     *
     * @param int $value QuantityReceived
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentItem instance
     */
    public function withQuantityReceived($value)
    {
        $this->setQuantityReceived($value);
        return $this;
    }


    /**
     * Checks if QuantityReceived is set
     *
     * @return bool true if QuantityReceived  is set
     */
    public function isSetQuantityReceived()
    {
        return !is_null($this->_fields['QuantityReceived']['FieldValue']);
    }

    /**
     * Gets the value of the QuantityInCase property.
     *
     * @return int QuantityInCase
     */
    public function getQuantityInCase()
    {
        return $this->_fields['QuantityInCase']['FieldValue'];
    }

    /**
     * Sets the value of the QuantityInCase property.
     *
     * @param int QuantityInCase
     * @return this instance
     */
    public function setQuantityInCase($value)
    {
        $this->_fields['QuantityInCase']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the QuantityInCase and returns this instance
     *
     * @param int $value QuantityInCase
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentItem instance
     */
    public function withQuantityInCase($value)
    {
        $this->setQuantityInCase($value);
        return $this;
    }


    /**
     * Checks if QuantityInCase is set
     *
     * @return bool true if QuantityInCase  is set
     */
    public function isSetQuantityInCase()
    {
        return !is_null($this->_fields['QuantityInCase']['FieldValue']);
    }




}
