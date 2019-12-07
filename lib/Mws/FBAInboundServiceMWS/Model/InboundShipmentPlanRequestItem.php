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
 * FBAInboundServiceMWS_Model_InboundShipmentPlanRequestItem
 *
 * Properties:
 * <ul>
 *
 * <li>SellerSKU: string</li>
 * <li>ASIN: string</li>
 * <li>Condition: string</li>
 * <li>Quantity: int</li>
 * <li>QuantityInCase: int</li>
 *
 * </ul>
 */
class Mws_FBAInboundServiceMWS_Model_InboundShipmentPlanRequestItem extends Mws_FBAInboundServiceMWS_Model
{

    /**
     * Construct new Mws_FBAInboundServiceMWS_Model_InboundShipmentPlanRequestItem
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>SellerSKU: string</li>
     * <li>ASIN: string</li>
     * <li>Condition: string</li>
     * <li>Quantity: int</li>
     * <li>QuantityInCase: int</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'SellerSKU' => array('FieldValue' => null, 'FieldType' => 'string'),
        'ASIN' => array('FieldValue' => null, 'FieldType' => 'string'),
        'Condition' => array('FieldValue' => null, 'FieldType' => 'string'),
        'Quantity' => array('FieldValue' => null, 'FieldType' => 'int'),
        'QuantityInCase' => array('FieldValue' => null, 'FieldType' => 'int'),
        );
        parent::__construct($data);
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
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentPlanRequestItem instance
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
     * Gets the value of the ASIN property.
     *
     * @return string ASIN
     */
    public function getASIN()
    {
        return $this->_fields['ASIN']['FieldValue'];
    }

    /**
     * Sets the value of the ASIN property.
     *
     * @param string ASIN
     * @return this instance
     */
    public function setASIN($value)
    {
        $this->_fields['ASIN']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the ASIN and returns this instance
     *
     * @param string $value ASIN
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentPlanRequestItem instance
     */
    public function withASIN($value)
    {
        $this->setASIN($value);
        return $this;
    }


    /**
     * Checks if ASIN is set
     *
     * @return bool true if ASIN  is set
     */
    public function isSetASIN()
    {
        return !is_null($this->_fields['ASIN']['FieldValue']);
    }

    /**
     * Gets the value of the Condition property.
     *
     * @return string Condition
     */
    public function getCondition()
    {
        return $this->_fields['Condition']['FieldValue'];
    }

    /**
     * Sets the value of the Condition property.
     *
     * @param string Condition
     * @return this instance
     */
    public function setCondition($value)
    {
        $this->_fields['Condition']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the Condition and returns this instance
     *
     * @param string $value Condition
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentPlanRequestItem instance
     */
    public function withCondition($value)
    {
        $this->setCondition($value);
        return $this;
    }


    /**
     * Checks if Condition is set
     *
     * @return bool true if Condition  is set
     */
    public function isSetCondition()
    {
        return !is_null($this->_fields['Condition']['FieldValue']);
    }

    /**
     * Gets the value of the Quantity property.
     *
     * @return int Quantity
     */
    public function getQuantity()
    {
        return $this->_fields['Quantity']['FieldValue'];
    }

    /**
     * Sets the value of the Quantity property.
     *
     * @param int Quantity
     * @return this instance
     */
    public function setQuantity($value)
    {
        $this->_fields['Quantity']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the Quantity and returns this instance
     *
     * @param int $value Quantity
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentPlanRequestItem instance
     */
    public function withQuantity($value)
    {
        $this->setQuantity($value);
        return $this;
    }


    /**
     * Checks if Quantity is set
     *
     * @return bool true if Quantity  is set
     */
    public function isSetQuantity()
    {
        return !is_null($this->_fields['Quantity']['FieldValue']);
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
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentPlanRequestItem instance
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
