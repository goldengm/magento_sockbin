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
 * FBAInboundServiceMWS_Model_InboundShipmentHeader
 *
 * Properties:
 * <ul>
 *
 * <li>ShipmentName: string</li>
 * <li>ShipFromAddress: FBAInboundServiceMWS_Model_Address</li>
 * <li>DestinationFulfillmentCenterId: string</li>
 * <li>AreCasesRequired: bool</li>
 * <li>ShipmentStatus: string</li>
 * <li>LabelPrepPreference: string</li>
 *
 * </ul>
 */
class Mws_FBAInboundServiceMWS_Model_InboundShipmentHeader extends Mws_FBAInboundServiceMWS_Model
{

    /**
     * Construct new Mws_FBAInboundServiceMWS_Model_InboundShipmentHeader
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>ShipmentName: string</li>
     * <li>ShipFromAddress: FBAInboundServiceMWS_Model_Address</li>
     * <li>DestinationFulfillmentCenterId: string</li>
     * <li>AreCasesRequired: bool</li>
     * <li>ShipmentStatus: string</li>
     * <li>LabelPrepPreference: string</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'ShipmentName' => array('FieldValue' => null, 'FieldType' => 'string'),

        'ShipFromAddress' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAInboundServiceMWS_Model_Address'),

        'DestinationFulfillmentCenterId' => array('FieldValue' => null, 'FieldType' => 'string'),
        'AreCasesRequired' => array('FieldValue' => null, 'FieldType' => 'bool'),
        'ShipmentStatus' => array('FieldValue' => null, 'FieldType' => 'string'),
        'LabelPrepPreference' => array('FieldValue' => null, 'FieldType' => 'string'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the ShipmentName property.
     *
     * @return string ShipmentName
     */
    public function getShipmentName()
    {
        return $this->_fields['ShipmentName']['FieldValue'];
    }

    /**
     * Sets the value of the ShipmentName property.
     *
     * @param string ShipmentName
     * @return this instance
     */
    public function setShipmentName($value)
    {
        $this->_fields['ShipmentName']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the ShipmentName and returns this instance
     *
     * @param string $value ShipmentName
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentHeader instance
     */
    public function withShipmentName($value)
    {
        $this->setShipmentName($value);
        return $this;
    }


    /**
     * Checks if ShipmentName is set
     *
     * @return bool true if ShipmentName  is set
     */
    public function isSetShipmentName()
    {
        return !is_null($this->_fields['ShipmentName']['FieldValue']);
    }

    /**
     * Gets the value of the ShipFromAddress.
     *
     * @return Address ShipFromAddress
     */
    public function getShipFromAddress()
    {
        return $this->_fields['ShipFromAddress']['FieldValue'];
    }

    /**
     * Sets the value of the ShipFromAddress.
     *
     * @param Address ShipFromAddress
     * @return void
     */
    public function setShipFromAddress($value)
    {
        $this->_fields['ShipFromAddress']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the ShipFromAddress  and returns this instance
     *
     * @param Address $value ShipFromAddress
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentHeader instance
     */
    public function withShipFromAddress($value)
    {
        $this->setShipFromAddress($value);
        return $this;
    }


    /**
     * Checks if ShipFromAddress  is set
     *
     * @return bool true if ShipFromAddress property is set
     */
    public function isSetShipFromAddress()
    {
        return !is_null($this->_fields['ShipFromAddress']['FieldValue']);

    }

    /**
     * Gets the value of the DestinationFulfillmentCenterId property.
     *
     * @return string DestinationFulfillmentCenterId
     */
    public function getDestinationFulfillmentCenterId()
    {
        return $this->_fields['DestinationFulfillmentCenterId']['FieldValue'];
    }

    /**
     * Sets the value of the DestinationFulfillmentCenterId property.
     *
     * @param string DestinationFulfillmentCenterId
     * @return this instance
     */
    public function setDestinationFulfillmentCenterId($value)
    {
        $this->_fields['DestinationFulfillmentCenterId']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the DestinationFulfillmentCenterId and returns this instance
     *
     * @param string $value DestinationFulfillmentCenterId
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentHeader instance
     */
    public function withDestinationFulfillmentCenterId($value)
    {
        $this->setDestinationFulfillmentCenterId($value);
        return $this;
    }


    /**
     * Checks if DestinationFulfillmentCenterId is set
     *
     * @return bool true if DestinationFulfillmentCenterId  is set
     */
    public function isSetDestinationFulfillmentCenterId()
    {
        return !is_null($this->_fields['DestinationFulfillmentCenterId']['FieldValue']);
    }

    /**
     * Gets the value of the AreCasesRequired property.
     *
     * @return bool AreCasesRequired
     */
    public function getAreCasesRequired()
    {
        return $this->_fields['AreCasesRequired']['FieldValue'];
    }

    /**
     * Sets the value of the AreCasesRequired property.
     *
     * @param bool AreCasesRequired
     * @return this instance
     */
    public function setAreCasesRequired($value)
    {
        $this->_fields['AreCasesRequired']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the AreCasesRequired and returns this instance
     *
     * @param bool $value AreCasesRequired
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentHeader instance
     */
    public function withAreCasesRequired($value)
    {
        $this->setAreCasesRequired($value);
        return $this;
    }


    /**
     * Checks if AreCasesRequired is set
     *
     * @return bool true if AreCasesRequired  is set
     */
    public function isSetAreCasesRequired()
    {
        return !is_null($this->_fields['AreCasesRequired']['FieldValue']);
    }

    /**
     * Gets the value of the ShipmentStatus property.
     *
     * @return string ShipmentStatus
     */
    public function getShipmentStatus()
    {
        return $this->_fields['ShipmentStatus']['FieldValue'];
    }

    /**
     * Sets the value of the ShipmentStatus property.
     *
     * @param string ShipmentStatus
     * @return this instance
     */
    public function setShipmentStatus($value)
    {
        $this->_fields['ShipmentStatus']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the ShipmentStatus and returns this instance
     *
     * @param string $value ShipmentStatus
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentHeader instance
     */
    public function withShipmentStatus($value)
    {
        $this->setShipmentStatus($value);
        return $this;
    }


    /**
     * Checks if ShipmentStatus is set
     *
     * @return bool true if ShipmentStatus  is set
     */
    public function isSetShipmentStatus()
    {
        return !is_null($this->_fields['ShipmentStatus']['FieldValue']);
    }

    /**
     * Gets the value of the LabelPrepPreference property.
     *
     * @return string LabelPrepPreference
     */
    public function getLabelPrepPreference()
    {
        return $this->_fields['LabelPrepPreference']['FieldValue'];
    }

    /**
     * Sets the value of the LabelPrepPreference property.
     *
     * @param string LabelPrepPreference
     * @return this instance
     */
    public function setLabelPrepPreference($value)
    {
        $this->_fields['LabelPrepPreference']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the LabelPrepPreference and returns this instance
     *
     * @param string $value LabelPrepPreference
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentHeader instance
     */
    public function withLabelPrepPreference($value)
    {
        $this->setLabelPrepPreference($value);
        return $this;
    }


    /**
     * Checks if LabelPrepPreference is set
     *
     * @return bool true if LabelPrepPreference  is set
     */
    public function isSetLabelPrepPreference()
    {
        return !is_null($this->_fields['LabelPrepPreference']['FieldValue']);
    }




}
