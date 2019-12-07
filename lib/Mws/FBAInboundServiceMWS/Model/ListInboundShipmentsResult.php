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
 * FBAInboundServiceMWS_Model_ListInboundShipmentsResult
 *
 * Properties:
 * <ul>
 *
 * <li>ShipmentData: FBAInboundServiceMWS_Model_InboundShipmentList</li>
 * <li>NextToken: string</li>
 *
 * </ul>
 */
class Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsResult extends Mws_FBAInboundServiceMWS_Model
{


    /**
     * Construct new Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsResult
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>ShipmentData: FBAInboundServiceMWS_Model_InboundShipmentList</li>
     * <li>NextToken: string</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'ShipmentData' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAInboundServiceMWS_Model_InboundShipmentList'),
        'NextToken' => array('FieldValue' => null, 'FieldType' => 'string'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the ShipmentData.
     *
     * @return InboundShipmentList ShipmentData
     */
    public function getShipmentData()
    {
        return $this->_fields['ShipmentData']['FieldValue'];
    }

    /**
     * Sets the value of the ShipmentData.
     *
     * @param InboundShipmentList ShipmentData
     * @return void
     */
    public function setShipmentData($value)
    {
        $this->_fields['ShipmentData']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the ShipmentData  and returns this instance
     *
     * @param InboundShipmentList $value ShipmentData
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsResult instance
     */
    public function withShipmentData($value)
    {
        $this->setShipmentData($value);
        return $this;
    }


    /**
     * Checks if ShipmentData  is set
     *
     * @return bool true if ShipmentData property is set
     */
    public function isSetShipmentData()
    {
        return !is_null($this->_fields['ShipmentData']['FieldValue']);

    }

    /**
     * Gets the value of the NextToken property.
     *
     * @return string NextToken
     */
    public function getNextToken()
    {
        return $this->_fields['NextToken']['FieldValue'];
    }

    /**
     * Sets the value of the NextToken property.
     *
     * @param string NextToken
     * @return this instance
     */
    public function setNextToken($value)
    {
        $this->_fields['NextToken']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the NextToken and returns this instance
     *
     * @param string $value NextToken
     * @return Mws_FBAInboundServiceMWS_Model_ListInboundShipmentsResult instance
     */
    public function withNextToken($value)
    {
        $this->setNextToken($value);
        return $this;
    }


    /**
     * Checks if NextToken is set
     *
     * @return bool true if NextToken  is set
     */
    public function isSetNextToken()
    {
        return !is_null($this->_fields['NextToken']['FieldValue']);
    }




}
