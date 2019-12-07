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
 * FBAInboundServiceMWS_Model_InboundShipmentPlan
 *
 * Properties:
 * <ul>
 *
 * <li>ShipmentId: string</li>
 * <li>DestinationFulfillmentCenterId: string</li>
 * <li>ShipToAddress: FBAInboundServiceMWS_Model_Address</li>
 * <li>LabelPrepType: string</li>
 * <li>Items: FBAInboundServiceMWS_Model_InboundShipmentPlanItemList</li>
 *
 * </ul>
 */
class Mws_FBAInboundServiceMWS_Model_InboundShipmentPlan extends Mws_FBAInboundServiceMWS_Model
{


    /**
     * Construct new Mws_FBAInboundServiceMWS_Model_InboundShipmentPlan
     *
     * @param mixed $data DOMElement or Associative Array to construct from.
     *
     * Valid properties:
     * <ul>
     *
     * <li>ShipmentId: string</li>
     * <li>DestinationFulfillmentCenterId: string</li>
     * <li>ShipToAddress: FBAInboundServiceMWS_Model_Address</li>
     * <li>LabelPrepType: string</li>
     * <li>Items: FBAInboundServiceMWS_Model_InboundShipmentPlanItemList</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'ShipmentId' => array('FieldValue' => null, 'FieldType' => 'string'),
        'DestinationFulfillmentCenterId' => array('FieldValue' => null, 'FieldType' => 'string'),
        'ShipToAddress' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAInboundServiceMWS_Model_Address'),
        'LabelPrepType' => array('FieldValue' => null, 'FieldType' => 'string'),
        'Items' => array('FieldValue' => null, 'FieldType' => 'Mws_FBAInboundServiceMWS_Model_InboundShipmentPlanItemList'),
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
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentPlan instance
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
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentPlan instance
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
     * Gets the value of the ShipToAddress.
     *
     * @return Address ShipToAddress
     */
    public function getShipToAddress()
    {
        return $this->_fields['ShipToAddress']['FieldValue'];
    }

    /**
     * Sets the value of the ShipToAddress.
     *
     * @param Address ShipToAddress
     * @return void
     */
    public function setShipToAddress($value)
    {
        $this->_fields['ShipToAddress']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the ShipToAddress  and returns this instance
     *
     * @param Address $value ShipToAddress
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentPlan instance
     */
    public function withShipToAddress($value)
    {
        $this->setShipToAddress($value);
        return $this;
    }


    /**
     * Checks if ShipToAddress  is set
     *
     * @return bool true if ShipToAddress property is set
     */
    public function isSetShipToAddress()
    {
        return !is_null($this->_fields['ShipToAddress']['FieldValue']);

    }

    /**
     * Gets the value of the LabelPrepType property.
     *
     * @return string LabelPrepType
     */
    public function getLabelPrepType()
    {
        return $this->_fields['LabelPrepType']['FieldValue'];
    }

    /**
     * Sets the value of the LabelPrepType property.
     *
     * @param string LabelPrepType
     * @return this instance
     */
    public function setLabelPrepType($value)
    {
        $this->_fields['LabelPrepType']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the LabelPrepType and returns this instance
     *
     * @param string $value LabelPrepType
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentPlan instance
     */
    public function withLabelPrepType($value)
    {
        $this->setLabelPrepType($value);
        return $this;
    }


    /**
     * Checks if LabelPrepType is set
     *
     * @return bool true if LabelPrepType  is set
     */
    public function isSetLabelPrepType()
    {
        return !is_null($this->_fields['LabelPrepType']['FieldValue']);
    }

    /**
     * Gets the value of the Items.
     *
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentPlanItemList Items
     */
    public function getItems()
    {
        return $this->_fields['Items']['FieldValue'];
    }

    /**
     * Sets the value of the Items.
     *
     * @param InboundShipmentPlanItemList Items
     * @return void
     */
    public function setItems($value)
    {
        $this->_fields['Items']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the Items  and returns this instance
     *
     * @param InboundShipmentPlanItemList $value Items
     * @return Mws_FBAInboundServiceMWS_Model_InboundShipmentPlan instance
     */
    public function withItems($value)
    {
        $this->setItems($value);
        return $this;
    }


    /**
     * Checks if Items  is set
     *
     * @return bool true if Items property is set
     */
    public function isSetItems()
    {
        return !is_null($this->_fields['Items']['FieldValue']);

    }




}
